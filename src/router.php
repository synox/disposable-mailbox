<?php

require_once './pages.php';

class Router {

    private $method;
    private $action;
    private $get_vars;
    private $post_vars;
    private $query_string;
    private $config;

    public function __construct(string $method, string $action = NULL, array $get_vars, array $post_vars, string $query_string, array $config) {
        $this->method = $method;
        $this->action = $action;
        $this->get_vars = $get_vars;
        $this->post_vars = $post_vars;
        $this->query_string = $query_string;
        $this->config = $config;
    }


    function route(): Page {
        if ($this->action === "redirect"
            && isset($this->post_vars['username'])
            && isset($this->post_vars['domain'])) {
            return new RedirectToAddressPage($this->post_vars['username'], $this->post_vars['domain'], $this->config['blocked_usernames']);

        } elseif ($this->action === "download_email"
            && isset($this->get_vars['download_email_id'])
            && isset($this->get_vars['address'])) {
            return new DownloadEmailPage($this->get_vars['download_email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->action === "delete_email"
            && isset($this->get_vars['delete_email_id'])
            && isset($this->get_vars['address'])) {
            return new DeleteEmailPage($this->get_vars['delete_email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->action === 'random') {
            return new RedirectToRandomAddressPage($this->config['domains']);

        } elseif (!empty($this->query_string)) {
            return new DisplayEmailsPage($this->query_string, $this->config);

        } else {
            return new RedirectToRandomAddressPage($this->config['domains']);
        }
    }
}