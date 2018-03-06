<?php
# set the new path of config.php (must be in a safe location outside the `public_html`)
require_once '../../config.php';

# load php dependencies:
require_once './backend-libs/autoload.php';

require_once './user.php';
require_once './imap_client.php';
require_once './controller.php';
require_once './router.rest.php';

$imapClient = new ImapClient($config['imap']['url'], $config['imap']['username'], $config['imap']['password']);

$router = new RestRouter($_SERVER['REQUEST_METHOD'], $_GET['action'] ?? NULL, $_GET, $_POST, $_SERVER['QUERY_STRING'], $config);
$page = $router->route();
$page->setViewHandler(new JsonViewHandler());
$page->invoke($imapClient);

// delete after each request
$imapClient->delete_old_messages($config['delete_messages_older_than']);

?>