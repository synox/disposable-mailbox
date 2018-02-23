<?php

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

