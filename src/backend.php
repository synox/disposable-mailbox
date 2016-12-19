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
 * @param $status http status
 * @param $text error text
 */
function error($status, $text) {
    @http_response_code($status);
    @print("{\"error\": \"$text\"}");
    die();
}

/**
 * print all mails for the given $user as a json string.
 * @param $username
 */
function print_inbox($username) {
    global $mailbox, $config;

    $name = clean_name($username);
    if (strlen($name) === 0) {
        error(400, 'invalid username');
    }
    $address = get_address($name, $config['mailHostname']);
    $mail_ids = search_mails($address, $mailbox);

    $emails = array();
    foreach ($mail_ids as $id) {
        $mail = $mailbox->getMail($id);
        // imap_search also returns partials matches. The mails have to be filtered again:
        if (!array_key_exists($address, $mail->to) && !array_key_exists($address, $mail->cc)) {
            continue;
        }
        $emails[] = $mail;
    }

    $data = array("mails" => $emails, 'username' => $name, 'address' => $address);
    print(json_encode($data));

}


/**
 * Search for mails with the recipient $to.
 * @return array mail ids
 */
function search_mails($address, $mailbox) {
    $filterTO = 'TO "' . $address . '"';
    $filterCC = 'CC "' . $address . '"';
    $mailsIdsTo = imap_sort($mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, $filterTO);
    $mailsIdsCc = imap_sort($mailbox->getImapStream(), SORTARRIVAL, true, SE_UID, $filterCC);
    return array_merge($mailsIdsTo, $mailsIdsCc);
}

/**
 * Remove illegal characters from username and remove everything after the @-sign. You may extend it if your server supports them.
 * @param $username
 * @return clean username
 */
function clean_name($username) {
    $username = strtolower($username);
    $username = preg_replace('/@.*$/', "", $username);   // remove part after @
    $username = preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters
    return $username;
}

/**
 * creates the full email address
 * @param $username
 * @param $domain
 * @return $username@$domain
 */
function get_address($username, $domain) {
    return $username . "@" . $domain;
}

/**
 * deletes messages older than X days.
 */
function delete_old_messages() {
    global $mailbox;

    $date = date('d-M-Y', strtotime('30 days ago'));
    $ids = $mailbox->searchMailbox('BEFORE ' . $date);
    foreach ($ids as $id) {
        $mailbox->deleteMail($id);
    }
    $mailbox->expungeDeletedMails();
}

/**
 * deletes emails by id and username. The username must match the id.
 *
 * @param $mailid internal id (integer)
 * @param $username the matching username
 */
function delete_mail($mailid, $username) {
    global $mailbox, $config;

    // in order to avoid https://www.owasp.org/index.php/Top_10_2013-A4-Insecure_Direct_Object_References
    // the $username must match the $mailid.
    $name = clean_name($username);
    if (strlen($name) === 0) {
        error(400, 'invalid username');
    }
    $address = get_address($name, $config['mailHostname']);
    $mail_ids = search_mails($address, $mailbox);

    if (in_array($mailid, $mail_ids)) {
        $mailbox->deleteMail($mailid);
        $mailbox->expungeDeletedMails();
        print(json_encode(array("success" => true)));
    } else {
        error(404, 'delete error: invalid username/mailid combination');
    }


}


header('Content-type: application/json');

// Never cache requests:
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


if (isset($_GET['username']) && isset($_GET['delete_email_id'])) {
    delete_mail($_GET['delete_email_id'], $_GET['username']);
} else if (isset($_GET['username'])) {
    print_inbox($_GET['username']);
} else {
    error(400, 'invalid action');
}

// run on every request
delete_old_messages();
