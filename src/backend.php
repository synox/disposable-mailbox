<?php
# set the new path of config.php (must be in a safe location outside the `public_html`)
require_once '../../config.php';

# load php dependencies:
require_once './backend-libs/autoload.php';

$mailbox = new PhpImap\Mailbox($config['imap']['url'],
    $config['imap']['username'],
    $config['imap']['password']);

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
 * @param $username string username
 * @param $address string email address
 */
function print_emails($username, $address) {
    global $mailbox;

    // Search for mails with the recipient $address in TO or CC.
    $mailsIdsTo = imap_sort($mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'TO "' . $address . '"');
    $mailsIdsCc = imap_sort($mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, 'CC "' . $address . '"');
    $mail_ids = array_merge($mailsIdsTo, $mailsIdsCc);

    $emails = _load_emails($mail_ids, $address);
    header('Content-type: application/json');
    print(json_encode(array("mails" => $emails, 'username' => $username, 'address' => $address)));
}


/**
 * deletes emails by id and username. The $address must match the recipient in the email.
 *
 * @param $mailid integer imap email id
 * @param $address string email address
 * @internal param the $username matching username
 */
function delete_email($mailid, $address) {
    global $mailbox;

    if (_load_one_email($mailid, $address) !== null) {
        $mailbox->deleteMail($mailid);
        $mailbox->expungeDeletedMails();
        header('Content-type: application/json');
        print(json_encode(array("success" => true)));
    } else {
        error(404, 'delete error: invalid username/mailid combination');
    }
}

/**
 * download email by id and username. The $address must match the recipient in the email.
 *
 * @param $mailid integer imap email id
 * @param $address string email address
 * @internal param the $username matching username
 */

function download_email($mailid, $address) {
    global $mailbox;

    if (_load_one_email($mailid, $address) !== null) {
        header("Content-Type: message/rfc822; charset=utf-8");
        header("Content-Disposition: attachment; filename=\"$address-$mailid.eml\"");

        $headers = imap_fetchheader($mailbox->getImapStream(), $mailid, FT_UID);
        $body = imap_body($mailbox->getImapStream(), $mailid, FT_UID);
        print ($headers . "\n" . $body);
    } else {
        error(404, 'download error: invalid username/mailid combination');
    }
}

/**
 * Load exactly one email, the $address in TO or CC has to match.
 * @param $mailid integer
 * @param $address String address
 * @return email or null
 */
function _load_one_email($mailid, $address) {
    // in order to avoid https://www.owasp.org/index.php/Top_10_2013-A4-Insecure_Direct_Object_References
    // the recipient in the email has to match the $address.
    $emails = _load_emails(array($mailid), $address);
    return count($emails) === 1 ? $emails[0] : null;
}

/**
 * Load emails using the $mail_ids, the mails have to match the $address in TO or CC.
 * @param $mail_ids array of integer ids
 * @param $address String address
 * @return array of emails
 */
function _load_emails($mail_ids, $address) {
    global $mailbox;

    $emails = array();
    foreach ($mail_ids as $id) {
        $mail = $mailbox->getMail($id);
        // imap_search also returns partials matches. The mails have to be filtered again:
        if (array_key_exists($address, $mail->to) || array_key_exists($address, $mail->cc)) {
            $emails[] = $mail;
        }
    }
    return $emails;
}

/**
 * Remove illegal characters from username and remove everything after the @-sign. You may extend it if your server supports them.
 * @param $username
 * @return string clean username
 */
function _clean_username($username) {
    $username = strtolower($username);
    $username = preg_replace('/@.*$/', "", $username);   // remove part after @
    return preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters
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

// Never cache requests:
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (isset($_GET['username'])) {
    // perform common validation:
    $username = _clean_username($_GET['username']);
    if (strlen($username) === 0) {
        error(400, 'invalid username');
    }
    $address = $username . "@" . $config['mailHostname'];

    // simple router:
    if (isset($_GET['download_email_id'])) {
        download_email($_GET['download_email_id'], $address);
    } else if (isset($_GET['delete_email_id'])) {
        delete_email($_GET['delete_email_id'], $address);
    } else {
        print_emails($username, $address);
    }
} else {
    error(400, 'invalid action');
}

// run on every request
delete_old_messages();
