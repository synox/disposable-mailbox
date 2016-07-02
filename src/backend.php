<?php
require_once './config.php';

# load php dependencies:
require_once './backend-libs/autoload.php';

$imap_settings = $config['imap'];
$mailbox = new PhpImap\Mailbox($imap_settings['url'], $imap_settings['username'], $imap_settings['password']);

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
    $to = get_address($name, $config['mailHostname']);
    $mail_ids = search_mails($to, $mailbox);

    $emails = array();
    foreach ($mail_ids as $id) {
        $emails[] = $mailbox->getMail($id);
    }
    $address = get_address($name, $config['mailHostname']);
    $data = array("mails" => $emails, 'username' => $name, 'address' => $address);
    print(json_encode($data));

}


/**
 * Search for mails with the recipient $to.
 * @return array mail ids
 */
function search_mails($to, $mailbox) {
    $filterTO = 'TO "' . $to . '"';
    $filterCC = 'CC "' . $to . '"';
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


header('Content-type: application/json');

// Never cache requests:
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_GET['action'])) {
    error(400, 'invalid parameter');
}
$action = $_GET['action'];

if ($action === "get" && isset($_GET['username'])) {
    print_inbox($_GET['username']);
} else {
    error(400, 'invalid action');
}

// run on every request
delete_old_messages();
