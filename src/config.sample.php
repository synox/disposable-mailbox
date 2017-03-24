<?php

// set your time zone:
date_default_timezone_set('Europe/Paris');

// enable while testing:
error_reporting(E_ALL);
// enable in production:
// error_reporting(0);

// configure this option if you want to allow requests from clients from other domains:
// see https://en.wikipedia.org/wiki/Cross-origin_resource_sharing
// header("Access-Control-Allow-Origin: *");

// Change IMAP settings (check SSL flags on http://php.net/manual/en/function.imap-open.php)
$config['imap']['url'] = '{example.com/imap/ssl}INBOX';
$config['imap']['username'] = "test";
$config['imap']['password'] = "test";

// email domain, usually different from imap hostname:
$config['mailHostname'] = "example.com";

// When to delete old messages?
$config['delete_messages_older_than'] = '30 days ago';