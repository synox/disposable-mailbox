<?php

// set your time zone:
date_default_timezone_set('Europe/Paris');

// enable in production:
error_reporting(0);

// enable while testing:
//error_reporting(E_ALL);


// Change IMAP settings (check SSL flags on http://php.net/manual/en/function.imap-open.php)
$config['imap']['url'] = '{imap.example.com/imap/ssl}INBOX';
$config['imap']['username'] = "myuser";
$config['imap']['password'] = "mypassword";

// email domains, usually different from imap hostname:
$config['domains'] = array('mydomain.com', 'example.com');

// When to delete old messages?
$config['delete_messages_older_than'] = '30 days ago';