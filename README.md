# disposable-mailbox
[![Join the chat at https://gitter.im/synox/disposable-mailbox](https://badges.gitter.im/synox/disposable-mailbox.svg)](https://gitter.im/synox/disposable-mailbox?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge)

A **self-hosted** disposable mailbox  service (aka trash mail)  :cloud: :envelope: 

**Demo**: [BhadooMail](https://bhadoomail.com/) 

![Screenshot](screenshot_example.png)


## Features

* Anonymous usage, generate random email addresses. 
* New Mail notification. Download and delete your emails.
* Display emails as text or html with sanitization  filter. 
* Display emails based on one [catch-all imap mailbox](https://www.google.ch/search?q=how+to+setup+catch-all+imap+mailbox).
* Only requires PHP  >=7.2 and [imap extension](http://php.net/manual/book.imap.php)

## Usage

### Requirements

* webserver with php >=7.2
* php [imap extension](http://php.net/manual/book.imap.php)
* IMAP account and a domain with [catch-all configuration](https://www.google.ch/search?q=how+to+setup+catch-all+imap+mailbox). (all emails go to one mailbox). 

### Before you start :heavy_exclamation_mark:

* Subscribe to [![Join the chat at https://gitter.im/synox/disposable-mailbox](https://badges.gitter.im/synox/disposable-mailbox.svg)](https://gitter.im/synox/disposable-mailbox?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) to be notified about issues and bugfixes.  
* This is **Beta** software, [there are still unsolved problems](https://github.com/synox/disposable-mailbox/issues). Contributions are welcome! :heart:
* License: **GPL-3.0**. You can modify this application and run it anywhere, charge money and show advertisement. Any forks or repacked distribution must follow the [GPL-3.0 license](https://opensource.org/licenses/GPL-3.0).  
* A link to https://github.com/synox/disposable-mailbox in the footer is appreciated.  



### Installation

Disposable-mailbox can be installed by copying the src directory to a webserver. 

1. assure the [imap extension](http://php.net/manual/book.imap.php) is installed. The following command should not print any errors:

        <?php print imap_base64("SU1BUCBleHRlbnNpb24gc2VlbXMgdG8gYmUgaW5zdGFsbGVkLiA="); ?>

2. download a [release](https://github.com/synox/disposable-mailbox/releases) or clone this repository
3. copy the files in the `src` directory to your web server (not the whole repo!).
4. rename `config.sample.php` to `config.php` and apply the imap settings. Move `config.php` to a safe location outside the `public_html`.
5. edit `index.php` and `json-api.php`: set the new path to `config.php`.
6. open it in your browser, check your php error log for messages. 

### Common Errors
* If you are using Cloudflare, and have a 500 Error, `novalidate-cert` to the imap url in `config.php`

### Build it yourself
The src directory contains all required files. If you want to update the php dependencies, you can update them yourself.  You must have [composer](https://getcomposer.org/download/) installed. 


Install php dependecies:

    composer update


## Credit :thumbsup:

This could not be possible without...

 * https://github.com/barbushin/php-imap, https://github.com/gnugat-legacy/PronounceableWord, http://htmlpurifier.org/, 
 * https://github.com/turbolinks/turbolinks, http://tobiasahlin.com/spinkit/

[![BrowserStack](browserstack.png)](https://www.browserstack.com/)

Supported by [BrowserStack](https://www.browserstack.com/), which allows us to test projects online with any browser as a service. :-) 
