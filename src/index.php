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
$imapClient->delete_old_messages($config['delete_messages_older_than']);


?>