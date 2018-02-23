<?php
# set the new path of config.php (must be in a safe location outside the `public_html`)
require_once '../../config.php';

# load php dependencies:
require_once './backend-libs/autoload.php';


require_once './user.php';
require_once './autolink.php';
require_once './pages.php';
require_once './router.php';
require_once './imap_client.php';

$router = new Router($_SERVER['REQUEST_METHOD'], $_GET['action'] ?? NULL, $_GET, $_POST, $_SERVER['QUERY_STRING'], $config);
$page = $router->route();

// TODO: remove $mailbox
$mailbox = new PhpImap\Mailbox($config['imap']['url'],
    $config['imap']['username'],
    $config['imap']['password']);

$imapClient = new ImapClient($config['imap']['url'], $config['imap']['username'], $config['imap']['password']);
$page->invoke($imapClient);

// delete after each request
delete_old_messages();

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


function _clean_domain($address) {
    $username = strtolower($address);
    $username = preg_replace('/^.*@/', "", $username);   // remove part before @
    return preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters
}

function redirect_to_random(array $domains) {
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


?>