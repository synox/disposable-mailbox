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