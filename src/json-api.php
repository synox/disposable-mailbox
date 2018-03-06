<?php
# set the new path of config.php (must be in a safe location outside the `public_html`)
require_once '../../config.php';

# load php dependencies:
require_once 'backend-libs/autoload.php';

require_once 'user.php';
require_once 'imap_client.php';
require_once 'controller.php';
require_once 'router.php';

class RestRouter extends Router {

    function route(): Controller {
        if ($this->method === "GET"
            && $this->action === "download_email"
            && isset($this->get_vars['email_id'])
            && isset($this->get_vars['address'])) {
            return new DownloadEmailController($this->get_vars['email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->method === "DELETE"
            && isset($this->get_vars['email_id'])
            && isset($this->get_vars['address'])) {
            return new DeleteEmailController($this->get_vars['email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->method === "GET"
            && $this->action === 'random_username') {
            return new RedirectToRandomAddressController($this->config['domains']);

        } elseif ($this->method === "GET"
            && $this->action === 'emails'
            && isset($this->get_vars['address'])) {
            return new DisplayEmailsController($this->get_vars['address'], $this->config);

        } else {
            return new InvalidRequestController();
        }
    }
}

class JsonViewHandler implements ViewHandler {

    private function json($obj) {
        header('Content-type: application/json');

        // Never cache requests:
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        print json_encode($obj);
        die();
    }

    function done($address) {
        $this->json(array('status' => "success"));
    }

    function error($status, $msg) {
        @http_response_code($status);
        $this->json(array('status' => "failure", 'error' => $msg));
    }

    function displayEmails($emails, $config, $user) {
        $this->json(array('status' => "success", 'emails' => $emails));
    }

    function newAddress($address) {
        $this->json(array('status' => "failure", 'address' => $address));
    }

    function downloadEmailAsRfc822($full_email, $filename) {
        $this->json(array('status' => "success", 'body' => $full_email));
    }

    function invalid_input($config_domains) {
        $this->error(400, 'Bad Request');
    }
}


$imapClient = new ImapClient($config['imap']['url'], $config['imap']['username'], $config['imap']['password']);

$router = new RestRouter($_SERVER['REQUEST_METHOD'], $_GET['action'] ?? NULL, $_GET, $_POST, $_SERVER['QUERY_STRING'], $config);
$controller = $router->route();
$controller->setViewHandler(new JsonViewHandler());
$controller->invoke($imapClient);

// delete after each request
$imapClient->delete_old_messages($config['delete_messages_older_than']);


?>