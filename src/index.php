<?php
# set the new path of config.php (must be in a safe location outside the `public_html`)
require_once '../../config.php';

# load php dependencies:
require_once './backend-libs/autoload.php';

$mailbox = new PhpImap\Mailbox($config['imap']['url'],
    $config['imap']['username'],
    $config['imap']['password']);


// simple router:
if (isset($_POST['username']) && isset($_POST['domain'])) {
    $user = User::parseUsernameAndDomain($_POST['username'], $_POST['domain']);
    header("location: ?" . $user->username . "@" . $user->domain);
    exit();
} elseif (isset($_GET['download_email_id']) && isset($_GET['address'])) {
    $user = User::parseDomain($_GET['address']);
    $download_email_id = filter_input(INPUT_GET, 'download_email_id', FILTER_SANITIZE_NUMBER_INT);
    if ($user->isInvalid()) {
        redirect_to_random($config['domains']);
        exit();
    }
    download_email($download_email_id, $user);
    exit();
} elseif (isset($_GET['delete_email_id']) && isset($_GET['address'])) {
    $user = User::parseDomain($_GET['address']);
    $delete_email_id = filter_input(INPUT_GET, 'delete_email_id', FILTER_SANITIZE_NUMBER_INT);
    if ($user->isInvalid()) {
        redirect_to_random($config['domains']);
        exit();
    }
    delete_email($delete_email_id, $user);
    header("location: ?" . $user->address);
    exit();
} elseif (isset($_GET['random'])) {
    redirect_to_random($config['domains']);
    exit();
} else {
    // print emails with html template
    $user = User::parseDomain($_SERVER['QUERY_STRING']);
    if ($user->isInvalid()) {
        redirect_to_random($config['domains']);
        exit();
    }
    $emails = get_emails($user);
    require "frontend.template.php";

    // delete after each request
    delete_old_messages();
}


/**
 * print error and stop program.
 * @param $status integer http status
 * @param $text string error text
 */
function error($status, $text) {
    @http_response_code($status);
    @print("{\"error\": \"$text\"}");
    die();
}

/**
 * print all mails for the given $user.
 * @param $user User
 * @return array
 */
function get_emails($user) {
    global $mailbox;

    // Search for mails with the recipient $address in TO or CC.
    $mailsIdsTo = imap_sort($mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'TO "' . $user->address . '"');
    $mailsIdsCc = imap_sort($mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'CC "' . $user->address . '"');
    $mail_ids = array_merge($mailsIdsTo, $mailsIdsCc);

    $emails = _load_emails($mail_ids, $user);
    return $emails;
}


/**
 * deletes emails by id and username. The address must match the recipient in the email.
 *
 * @param $mailid integer imap email id
 * @param $user User
 * @internal param the $username matching username
 */
function delete_email($mailid, $user) {
    global $mailbox;

    if (_load_one_email($mailid, $user) !== null) {
        $mailbox->deleteMail($mailid);
        $mailbox->expungeDeletedMails();
    } else {
        error(404, 'delete error: invalid username/mailid combination');
    }
}

/**
 * download email by id and username. The $address must match the recipient in the email.
 *
 * @param $mailid integer imap email id
 * @param $user User
 * @internal param the $username matching username
 */

function download_email($mailid, $user) {
    global $mailbox;

    if (_load_one_email($mailid, $user) !== null) {
        header("Content-Type: message/rfc822; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"" . $user->address . "-" . $mailid . ".eml\"");

        $headers = imap_fetchheader($mailbox->getImapStream(), $mailid, FT_UID);
        $body = imap_body($mailbox->getImapStream(), $mailid, FT_UID);
        print $headers . "\n" . $body;
    } else {
        error(404, 'download error: invalid username/mailid combination');
    }
}

/**
 * Load exactly one email, the $address in TO or CC has to match.
 * @param $mailid integer
 * @param $user User
 * @return email or null
 */
function _load_one_email($mailid, $user) {
    // in order to avoid https://www.owasp.org/index.php/Top_10_2013-A4-Insecure_Direct_Object_References
    // the recipient in the email has to match the $address.
    $emails = _load_emails(array($mailid), $user);
    return count($emails) === 1 ? $emails[0] : null;
}

/**
 * Load emails using the $mail_ids, the mails have to match the $address in TO or CC.
 * @param $mail_ids array of integer ids
 * @param $user User
 * @return array of emails
 */
function _load_emails($mail_ids, $user) {
    global $mailbox;

    $emails = array();
    foreach ($mail_ids as $id) {
        $mail = $mailbox->getMail($id);
        // imap_search also returns partials matches. The mails have to be filtered again:
        if (array_key_exists($user->address, $mail->to) || array_key_exists($user->address, $mail->cc)) {
            $emails[] = $mail;
        }
    }
    return $emails;
}

/**
 * Remove illegal characters from address.
 * @param $address
 * @return string clean address
 */
function _clean_address($address) {
    return strtolower(filter_var($address, FILTER_SANITIZE_EMAIL));
}


/**
 * Remove illegal characters from username and remove everything after the @-sign. You may extend it if your server supports them.
 * @param $address
 * @return string clean username
 */
function _clean_username($address) {
    global $config;
    $username = strtolower($address);
    $username = preg_replace('/@.*$/', "", $username);   // remove part after @
    $username = preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters

    if (in_array($username, $config['blocked_usernames'])) {
        // Forbidden name!
        return '';
    }

    return $username;
}

class User {
    public $address;
    public $username;
    public $domain;

    public function isInvalid() {
        global $config;
        if (empty($this->username) || empty($this->domain)) {
            return true;
        } else if (!in_array($this->domain, $config['domains'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function parseDomain($address) {
        $clean_address = _clean_address($address);
        $user = new User();
        $user->username = _clean_username($clean_address);
        $user->domain = _clean_domain($clean_address);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }

    public static function parseUsernameAndDomain($username, $domain) {
        $user = new User();
        $user->username = _clean_username($username);
        $user->domain = _clean_domain($domain);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }
}


function _clean_domain($address) {
    $username = strtolower($address);
    $username = preg_replace('/^.*@/', "", $username);   // remove part before @
    return preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters
}

function redirect_to_random($domains) {
    $wordLength = rand(3, 8);
    $container = new PronounceableWord_DependencyInjectionContainer();
    $generator = $container->getGenerator();
    $word = $generator->generateWordOfGivenLength($wordLength);
    $nr = rand(51, 91);
    $name = $word . $nr;

    $domain = $domains[array_rand($domains)];
    header("location: ?$name@$domain");
}

/**
 * deletes messages older than X days.
 */
function delete_old_messages() {
    global $mailbox, $config;

    $ids = $mailbox->searchMailbox('BEFORE ' . date('d-M-Y', strtotime($config['delete_messages_older_than'])));
    foreach ($ids as $id) {
        $mailbox->deleteMail($id);
    }
    $mailbox->expungeDeletedMails();
}


class AutoLinkExtension {
    static public function auto_link_text($string) {

        $string = preg_replace_callback("/
            ((?<![\"'])                                     # don't look inside quotes
            (\b
            (                           # protocol or www.
                [a-z]{3,}:\/\/
            |
                www\.
            )
            (?:                         # domain
                [a-zA-Z0-9_\-]+
                (?:\.[a-zA-Z0-9_\-]+)*
            |
                localhost
            )
            (?:                         # port
                 \:[0-9]+
            )?
            (?:                         # path
                \/[a-z0-9:%_|~.-]*
                (?:\/[a-z0-9:%_|~.-]*)*
            )?
            (?:                         # attributes
                \?[a-z0-9:%_|~.=&#;-]*
            )?
            (?:                         # anchor
                \#[a-z0-9:%_|~.=&#;-]*
            )?
            )
            (?![\"']))
            /ix",
            function ($match) {
                $url = $match[0];
                $href = $url;

                if (false === strpos($href, 'http')) {
                    $href = 'http://' . $href;
                }
                return '<a href="' . $href . '" rel="noreferrer">' . $url . '</a>';
            }, $string);

        return $string;
    }

}

?>