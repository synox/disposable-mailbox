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
        $clean_address = User::_clean_address($address);
        $user = new User();
        $user->username = User::_clean_username($clean_address);
        $user->domain = User::_clean_domain($clean_address);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }

    public static function parseUsernameAndDomain(string $username, string $domain): User {
        $user = new User();
        $user->username = User::_clean_username($username);
        $user->domain = User::_clean_domain($domain);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }

    /**
     * Remove illegal characters from address.
     * @param $address
     * @return string clean address
     */
    private static function _clean_address($address) {
        return strtolower(filter_var($address, FILTER_SANITIZE_EMAIL));
    }


    /**
     * Remove illegal characters from username and remove everything after the @-sign. You may extend it if your server supports them.
     * @param $address
     * @return string clean username
     */
    private static function _clean_username($address) {
        global $config;
        $username = strtolower($address);
        $username = preg_replace('/@.*$/', "", $username);   // remove part after @
        $username = preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters

        if (in_array($username, $config['blocked_usernames'])) {
            // Forbidden name!
            return '';
        }

        return $username;
    }


    private static function _clean_domain($address) {
        $username = strtolower($address);
        $username = preg_replace('/^.*@/', "", $username);   // remove part before @
        return preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters
    }

}
