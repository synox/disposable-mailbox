<?php

require_once './pages.php';

class Router {

    private $method;
    private $action;
    private $get_vars;
    private $post_vars;
    private $query_string;
    private $config;

    public function __construct($method, $action, $get_vars, $post_vars, $query_string, $config) {
        $this->method = $method;
        $this->action = $action;
        $this->get_vars = $get_vars;
        $this->post_vars = $post_vars;
        $this->query_string = $query_string;
        $this->config = $config;
    }

    static function init() {
        global $config;
        return new Router($_SERVER['REQUEST_METHOD'], isset($_GET['action']) ? $_GET['action'] : null, $_GET, $_POST, $_SERVER['QUERY_STRING'], $config);
    }


    function route() {
        // TODO: use $this->action
        if (isset($this->post_vars['username']) && isset($this->post_vars['domain'])) {
            return new RedirectToAddressPage($this->post_vars['username'], $this->post_vars['domain']);
        } elseif (isset($this->get_vars['download_email_id']) && isset($this->get_vars['address'])) {
            return new DownloadEmailPage($this->get_vars['download_email_id'], $this->get_vars['address'], $this->config['domains']);
        } elseif (isset($this->get_vars['delete_email_id']) && isset($this->get_vars['address'])) {
            return new DeleteEmailPage($this->get_vars['delete_email_id'], $this->get_vars['address'], $this->config['domains']);
        } elseif (isset($this->get_vars['random'])) {
            return new RedirectToRandomAddressPage($this->config['domains']);
        } elseif (empty($this->query_string)) {
            return new RedirectToRandomAddressPage($this->config['domains']);
        } elseif (!empty($this->query_string)) {
            return new DisplayEmailsPage($this->query_string, $this->config);
        } else {
            return new InvalidRequestPage();
        }
    }
}