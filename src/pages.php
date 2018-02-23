<?php

abstract class Page {

    function invoke(ImapClient $imapClient) {
    }

    function if_invalid_redirect_to_random(User $user, array $config_domains) {
        if ($user->isInvalid()) {
            redirect_to_random($config_domains);
            exit();
        }
    }
}

class RedirectToAddressPage extends Page {
    private $username;
    private $domain;

    public function __construct(string $username, string $domain) {
        $this->username = $username;
        $this->domain = $domain;
    }

    function invoke(ImapClient $imapClient) {
        $user = User::parseUsernameAndDomain($this->username, $this->domain);
        header("location: ?" . $user->username . "@" . $user->domain);
    }
}

class DownloadEmailPage extends Page {

    private $email_id;
    private $address;
    private $config_domains;

    public function __construct(string $email_id, string $address, array $config_domains) {
        $this->email_id = $email_id;
        $this->address = $address;
        $this->config_domains = $config_domains;
    }


    function invoke(ImapClient $imapClient) {
        $user = User::parseDomain($this->address);
        $this->if_invalid_redirect_to_random($user, $this->config_domains);

        $download_email_id = filter_var($this->email_id, FILTER_SANITIZE_NUMBER_INT);
        $imapClient->download_email($download_email_id, $user);
    }
}


class DeleteEmailPage extends Page {
    private $email_id;
    private $address;
    private $config_domains;

    public function __construct($email_id, $address, $config_domains) {
        $this->email_id = $email_id;
        $this->address = $address;
        $this->config_domains = $config_domains;
    }

    function invoke(ImapClient $imapClient) {
        $user = User::parseDomain($this->address);
        $this->if_invalid_redirect_to_random($user, $this->config_domains);

        $delete_email_id = filter_var($this->email_id, FILTER_SANITIZE_NUMBER_INT);
        $imapClient->delete_email($delete_email_id, $user);
        header("location: ?" . $user->address);
    }
}

class RedirectToRandomAddressPage extends Page {
    private $config_domains;

    public function __construct($config_domains) {
        $this->config_domains = $config_domains;
    }

    function invoke(ImapClient $imapClient) {
        redirect_to_random($this->config_domains);
    }

}

class DisplayEmailsPage extends Page {
    private $address;
    private $config;

    public function __construct($address, $config) {
        $this->address = $address;
        $this->config = $config;
    }


    function invoke(ImapClient $imapClient) {
        // print emails with html template
        $user = User::parseDomain($this->address);
        $this->if_invalid_redirect_to_random($user, $this->config['domains']);

        global $emails;
        global $config;
        $emails = $imapClient->get_emails($user);
        require "frontend.template.php";
    }
}

class InvalidRequestPage extends Page {
    function invoke(ImapClient $imapClient) {
        error(400, "Bad Request");
    }
}
