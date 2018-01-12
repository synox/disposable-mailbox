Tests
=====

In order to keep PronounceableWordGenerator stable and to maintain a high
quality, tests have been written using PHPUnit >= 3.5 
(https://github.com/sebastianbergmann/phpunit/).

Installation
============

Before doing any test, you must do some installations.

Installing PEAR
---------------

PEAR (http://pear.php.net/) is necessary to use PHPUnit. To install it, follow
these instructions: http://pear.php.net/manual/en/installation.getting.php

If you are on Windows, and using WAMP or EasyPHP (or maybe others web
development plateforms), you might encounter the following error::

    phar "C:\wamp\bin\php\php5.3.0\PEAR\go-pear.phar" does not have a signature PHP Warning: require_once(phar://go-pear.par/index.php): failed to open stream: phar error: invalid url or non-existent phar "phar://go-pear.phar/index.php" in C:\wamp\bin\php\php5.3.0\PEAR\go-pear.phar on line 1236

    Warning: require_once(phar://go-pear.par/index.php): failed to open stream: phar error: invalid url or non-existent phar "phar://go-pear.phar/index.php" in C:\wamp\bin\php\php5.3.0\PEAR\go-pear.phar on line 1236 Press any key to continue...

This is because the PHP setting "phar.require_hash" is set to "On" by default.
If you set it to "Off" in your "php.ini", you should be able to continue.

Installing PHPUnit
------------------

Once PEAR is installed, you can use it to install PHPUnit by following these
instructions: http://www.phpunit.de/manual/3.0/en/installation.html

Running the tests
=================

Tests can be run by CLI, using the following command::

  phpunit ./test
