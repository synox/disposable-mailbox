PronounceableWord PHP library
=============================

A light, customizable and simple PHP (>= 5.2) library, used to generate
randomly pronounceable words, names and passwords without using database of
existing words or big samples of text like with Markov chains.

**Warning**

Offensive or insulting words might be generated because of the random nature
of the generator (see how to manage them in
``./doc/manageOffensiveAndInsultingWords.rst``).

Installation and usage
======================

First, get the last stable version, and put it in an accessible directory.

You can use composer to do so (http://packagist.org/), by adding in the
``require`` of your file ``./composer.json`` the following entry:
``"gnugat/PronounceableWord": "*"``.

You should have a fully operationnal pronounceable word generator::

    <?php
    // File "/index.php".
    
    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/DependencyInjectionContainer.php';

    define('MINIMUM_LENGTH', 5);
    define('MAXIMUM_LENGTH', 11);

    $length = rand(MINIMUM_LENGTH, MAXIMUM_LENGTH);

    $container = new PronounceableWord_DependencyInjectionContainer();
    $generator = $container->getGenerator();
    $word = $generator->generateWordOfGivenLength($length);

Configuration
-------------

To customize the algorithm, the letters used, the linked letters or the types,
just copy and modify as you wish the files in the
``./src/PronounceableWord/Configuration`` directory and then pass your
configuration classes to the container (see how to configure in
``./doc/configuration.rst``).

Tests
-----

Tests are done using PHPUnit (https://github.com/sebastianbergmann/phpunit/)
(>=3.5) in the ``./test`` directory (see how to test in ``./doc/tests.rst``).

Examples
--------

Here is a sample of examples that can be generated (use the
``./bin/generateExamples.php`` script for more):

======= ====== ======= ======== =========
Length  5      6       7        8
======= ====== ======= ======== =========
1       vicas  panori  eropops  absugrit
2       ramsa  verifu  simbous  nimpiowi
3       kiclu  impriar rubleru  entumecu
4       posup  bivary  kitocra  vemolior
5       utler  rugisi  kanomev  pactabie
======= ====== ======= ======== =========

Algorithm
=========

Basically, the library will generate a word following these rules:

1. Choose randomly a letter;
2. choose randomly a linked letter of different type;
3. choose randomly a linked letter, of different type if the last letter is
   of consecutive types.

Where:

* "linked letter" is an arbitrary chosen letter that is expected to follow
  well the previous letter;
* "types" would be voyels and consonants;
* "consecutive" would be a group of two letters from the same "type".

The step 3 is repeated as many times as necessary.

Documentation
=============

You can find more documentation at the following links:

* Copyright and MIT license: ``./LICENSE.rst``;
* version and change log: ``./VERSION.rst`` and ``CHANGELOG.rst``;
* technical and usage documentation: ``./doc/``.

Contributing
============

1. Fork it: https://github.com/gnugat/PronounceableWord/fork_select ;
2. create a branch (``git checkout -b my_branch``);
3. commit your changes (``git commit -am "Changes description message"``);
4. push to the branch (``git push origin my_branch``);
5. create an Issue (https://github.com/gnugat/PronounceableWord/issues) with a
   link to your branch;
6. wait for it to be accepted/argued.
