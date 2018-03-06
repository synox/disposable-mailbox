<?php

require_once './controller.php';

class Router {

    protected $method;
    protected $action;
    protected $get_vars;
    protected $post_vars;
    protected $query_string;
    protected $config;

    public function __construct(string $method, string $action = NULL, array $get_vars, array $post_vars, string $query_string, array $config) {
        $this->method = $method;
        $this->action = $action;
        $this->get_vars = $get_vars;
        $this->post_vars = $post_vars;
        $this->query_string = $query_string;
        $this->config = $config;
    }


    function route(): Controller {
        if ($this->action === "redirect"
            && isset($this->post_vars['username'])
            && isset($this->post_vars['domain'])) {
            return new RedirectToAddressController($this->post_vars['username'], $this->post_vars['domain'], $this->config['blocked_usernames']);

        } elseif ($this->action === "download_email"
            && isset($this->get_vars['email_id'])
            && isset($this->get_vars['address'])) {
            return new DownloadEmailController($this->get_vars['email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->action === "delete_email"
            && isset($this->get_vars['email_id'])
            && isset($this->get_vars['address'])) {
            return new DeleteEmailController($this->get_vars['email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->action === 'random') {
            return new RedirectToRandomAddressController($this->config['domains']);

        } elseif (!empty($this->query_string)) {
            return new DisplayEmailsController($this->query_string, $this->config);

        } else {
            return new RedirectToRandomAddressController($this->config['domains']);
        }
    }
}