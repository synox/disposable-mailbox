<?php

date_default_timezone_set('Europe/Paris');
error_reporting(0);

// configure this option if you want to allow requests from clients from other domains:
// see https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
// header("Access-Control-Allow-Origin: *");

// setup imap connection
$config['imap']['host'] = "localhost";
$config['imap']['url'] = '{' . $config['imap']['host'] . '/imap/ssl}INBOX';
$config['imap']['username'] = "test";
$config['imap']['password'] = "test";

// email domain, usually different from imap hostname:
$config['mailHostname'] = "example.com";
