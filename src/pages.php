<?php

abstract class Page {

    function invoke() {
    }
}

class RedirectToAddressPage extends Page {
    private $username;
    private $domain;

    public function __construct($username, $domain) {
        $this->username = $username;
        $this->domain = $domain;
    }

    function invoke() {
        $user = User::parseUsernameAndDomain($this->username, $this->domain);
        header("location: ?" . $user->username . "@" . $user->domain);
        exit();
    }
}

class DownloadEmailPage extends Page {

    private $email_id;
    private $address;
    private $config_domains;

    public function __construct($email_id, $address, $config_domains) {
        $this->email_id = $email_id;
        $this->address = $address;
        $this->config_domains = $config_domains;
    }


    function invoke() {
        $user = User::parseDomain($this->address);
        $download_email_id = filter_var($this->email_id, FILTER_SANITIZE_NUMBER_INT);
        if ($user->isInvalid()) {
            redirect_to_random($this->config_domains);
            exit();
        }
        download_email($download_email_id, $user);
        exit();
    }
}


class DeleteEmailPage extends Page {
    private $email_id;
    private $address;
    private $all_domains;

    public function __construct($email_id, $address, $all_domains) {
        $this->email_id = $email_id;
        $this->address = $address;
        $this->all_domains = $all_domains;
    }

    function invoke() {
        $user = User::parseDomain($this->address);
        $delete_email_id = filter_var($this->email_id, FILTER_SANITIZE_NUMBER_INT);
        if ($user->isInvalid()) {
            redirect_to_random($this->all_domains);
            exit();
        }
        delete_email($delete_email_id, $user);
        header("location: ?" . $user->address);
        exit();
    }
}

class RedirectToRandomAddressPage extends Page {
    private $all_domains;

    public function __construct($all_domains) {
        $this->all_domains = $all_domains;
    }

    function invoke() {
        redirect_to_random($this->all_domains);
        exit();
    }

}

class DisplayEmailsPage extends Page {
    private $address;
    private $config;

    public function __construct($address, $config) {
        $this->address = $address;
        $this->config = $config;
    }


    function invoke() {
        // print emails with html template
        $user = User::parseDomain($this->address);
        if ($user->isInvalid()) {
            redirect_to_random($this->config['domains']);
            exit();
        }
        global $emails;
        global $config;
        $emails = get_emails($user);
        require "frontend.template.php";

    }
}

class InvalidRequestPage extends Page {
    function invoke() {
        error(400, "Bad Request");
    }
}