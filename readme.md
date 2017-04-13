# self-hosted disposable email service

[![Join the chat at https://gitter.im/synox/disposable-mailbox](https://badges.gitter.im/synox/disposable-mailbox.svg)](https://gitter.im/synox/disposable-mailbox?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

## Goals:
 * easy to use: random or custom name, auto refresh
 * easy to host: just php5 with imap extension, catch-all mailbox
 * easy to install: copy-paste and imap config
 * minimal code base: minimal features and complexity

| ![Screenshot](screenshot.png)        | 
| ------------- | 


## You have to know

* Use [![Join the chat at https://gitter.im/synox/disposable-mailbox](https://badges.gitter.im/synox/disposable-mailbox.svg)](https://gitter.im/synox/disposable-mailbox?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) and you will be be notified about issues and bugfixes.  
* This is **WORK IN PROGRESS (WIP)** software, do not use it in production yet! [There are still unsolved problems](https://github.com/synox/disposable-mailbox/issues). Contributions are welcome!
* Licence: <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/"><img alt="Creative Commons License" style="border-width:0" src="https://i.creativecommons.org/l/by-nc-sa/4.0/88x31.png" /></a><br /><span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">disposable-mailbox</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="https://github.com/synox/disposable-mailbox" property="cc:attributionName" rel="cc:attributionURL">github:synox</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-sa/4.0/">Creative Commons Attribution-NonCommercial-ShareAlike 4.0 International License</a>.


## Requirements

* apache 2 webserver with php >=5.3.0 (let me know how it works on nginx!)
* php [imap extension](http://php.net/manual/book.imap.php)
* IMAP account and a domain with catch-all configuration. (all mails go to one mailbox). 

## Installation

1. assure the [imap extension](http://php.net/manual/book.imap.php) is installed. The following command should not print any errors:

        <?php print imap_base64("SU1BUCBleHRlbnNpb24gc2VlbXMgdG8gYmUgaW5zdGFsbGVkLiA="); ?>

2. download a [release](https://github.com/synox/disposable-mailbox/releases) or clone this repository
3. copy the files in the `src` directory to your web server (not the whole repo!).
4. rename `config.sample.php` to `config.php` and apply the imap settings. Move `config.php` to a safe location outside the `public_html`.
5. edit `backend.php` and set the new path to `config.php`.
6. open it in your browser, check your php error log for messages. 


## Build it yourself
The src directory contains all required files. If you want to update the php dependencies, you can update them yourself.  You must have [composer](https://getcomposer.org/download/) installed. 


Install php dependecies:

    composer install

## Credit

This could not be possible without...

 * http://angularjs.org/
 * https://github.com/barbushin/php-imap
 * https://github.com/gregjacobs/Autolinker.js/
 * http://chancejs.com/
