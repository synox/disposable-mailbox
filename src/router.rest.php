<?php

require_once './controller.php';
require_once './router.php';


class RestRouter extends Router {

    function route(): Controller {
        if ($this->action === "download_email"
            && isset($this->get_vars['email_id'])
            && isset($this->get_vars['address'])) {
            return new DownloadEmailController($this->get_vars['email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->action === "delete_email"
            && isset($this->get_vars['email_id'])
            && isset($this->get_vars['address'])) {
            return new DeleteEmailController($this->get_vars['email_id'], $this->get_vars['address'], $this->config['domains'], $this->config['blocked_usernames']);

        } elseif ($this->action === 'get_random_username') {
            return new RedirectToRandomAddressController($this->config['domains']);

        } elseif ($this->action === 'get_emails' && isset($this->get_vars['address'])) {
            return new DisplayEmailsController($this->get_vars['address'], $this->config);

        } else {
            return new InvalidRequestController();
        }
    }
}