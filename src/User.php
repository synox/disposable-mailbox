<?php

class User {
    public $address;
    public $username;
    public $domain;

    public static function get_random_address(array $domains): string {
        $wordLength = rand(3, 8);
        $container = new PronounceableWord_DependencyInjectionContainer();
        $generator = $container->getGenerator();
        $word = $generator->generateWordOfGivenLength($wordLength);
        $nr = rand(51, 91);
        $name = $word . $nr;

        $domain = $domains[array_rand($domains)];
        return "$name@$domain";
    }

    public function isInvalid(array $config_domains): bool {
        if (empty($this->username) || empty($this->domain)) {
            return true;
        } elseif (!in_array($this->domain, $config_domains)) {
            return true;
        } else {
            return false;
        }
    }

    public static function parseDomain(string $address, array $config_blocked_usernames): User {
        $clean_address = User::_clean_address($address);
        $user = new User();
        $user->username = User::_clean_username($clean_address, $config_blocked_usernames);
        $user->domain = User::_clean_domain($clean_address);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }

    public static function parseUsernameAndDomain(string $username, string $domain, $config_blocked_usernames): User {
        $user = new User();
        $user->username = User::_clean_username($username, $config_blocked_usernames);
        $user->domain = User::_clean_domain($domain);
        $user->address = $user->username . '@' . $user->domain;
        return $user;
    }

    /**
     * Remove illegal characters from address.
     * @return string clean address
     */
    private static function _clean_address(string $address) {
        return strtolower(filter_var($address, FILTER_SANITIZE_EMAIL));
    }


    /**
     * Remove illegal characters from username and remove everything after the @-sign. You may extend it if your server supports them.
     *
     * @return string clean username
     */
    private static function _clean_username(string $address, array $config_blocked_usernames) {
        $username = strtolower($address);
        $username = preg_replace('/@.*$/', "", $username);   // remove part after @
        $username = preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters

        if (in_array($username, $config_blocked_usernames)) {
            // Forbidden name!
            return '';
        }

        return $username;
    }


    private static function _clean_domain(string $address) {
        $username = strtolower($address);
        $username = preg_replace('/^.*@/', "", $username);   // remove part before @
        return preg_replace('/[^A-Za-z0-9_.+-]/', "", $username);   // remove special characters
    }
}
