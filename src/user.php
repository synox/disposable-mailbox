<?php

class User {
    public $address;
    public $username;
    public $domain;

    public function isInvalid(): bool {
        global $config;
        if (empty($this->username) || empty($this->domain)) {
            return true;
        } else if (!in_array($this->domain, $config['domains'])) {
            return true;
        } else {
            return false;
        }
    }

    public static function parseDomain(string $address): User {
        $clean_address = _clean_address($address);
        $user = new User();
        $user->username = _clean_username($clean_address);
        $user->domain = _clean_domain($clean_address);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }

    public static function parseUsernameAndDomain(string $username, string $domain): User {
        $user = new User();
        $user->username = _clean_username($username);
        $user->domain = _clean_domain($domain);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }
}
