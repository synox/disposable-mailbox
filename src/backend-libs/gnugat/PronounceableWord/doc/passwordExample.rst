The password generator example
==============================

This page is intended to provide an example of usage and configuration, to
generate passwords with PronounceableWord.

First create your project. You should have something like::

    <?php
    // File "/index.php".


Installation
============

First, get the last stable version, and put it in an accessible directory::

    <?php
    // File "/index.php".

    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/Generator.php';

For now, you should have a fully operationnal generator::

    <?php
    // File "/index.php".

    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/DependencyInjectionContainer.php';

    define('MINIMUM_LENGTH', 5);
    define('MAXIMUM_LENGTH', 11);

    $length = rand(MINIMUM_LENGTH, MAXIMUM_LENGTH);

    $container = new PronounceableWord_DependencyInjectionContainer();
    $generator = $container->getGenerator();
    $password = $generator->generateWordOfGivenLength($length);

Configuration
=============

Let's say that the standard configuration doesn't please you, because you think
that password should also contain integers.

Linked letters configuration
----------------------------

The first thing is to create your own linked letters configuration, with
integers::

    <?php
    // File "./Configuration/LinkedLetters.php"
    class My_Configuration_LinkedLetters {
        public $lettersWithLinkedLetters = array(
            'a' => 'bcdgiklmnprstvy0123456789',
            'b' => 'aeilorstuy0123456789',
            'c' => 'acehiklortu0123456789',
            'd' => 'aeiorsu0123456789',
            'e' => 'acdeilmnprstvxy0123456789',
            'f' => 'aefilortu0123456789',
            'g' => 'aeghilnorsu0123456789',
            'h' => 'aeiortu0123456789',
            'i' => 'acdefglmnorstv0123456789',
            'k' => 'aeilnos0123456789',
            'l' => 'adefilostuy0123456789',
            'm' => 'abeimoprsuy0123456789',
            'n' => 'acdegiosty0123456789',
            'o' => 'cdfklmnoprstuvw0123456789',
            'p' => 'aehiloprstu0123456789',
            'r' => 'acdegilmnorstuy0123456789',
            's' => 'acehilopstu0123456789',
            't' => 'aehilorstuy0123456789',
            'u' => 'abcdegilmnprst0123456789',
            'v' => 'aeino0123456789',
            'w' => 'aehinos0123456789',
            'x' => 'acehiptu0123456789',
            'y' => 'eiost0123456789',
            'z' => 'aeiloyz0123456789',
            '0' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '1' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '2' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '3' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '4' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '5' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '6' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '7' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '8' => 'abcdefghiklmnoprstuvwxyz0123456789',
            '9' => 'abcdefghiklmnoprstuvwxyz0123456789',
        );
    }

Letter type configuration
-------------------------

Then create the letter types configuration with integers::

    <?php
    // File "./Configuration/LetterTypes.php"
    class My_Configuration_LetterTypes {
        public $letterTypesWithLetters = array(
            'voyels' => 'aeiouy',
            'consonants' => 'bcdfghklmnprstvwxz',
            'integers' => '0123456789',
        );
    }

Adding your configuration
-------------------------

Finally, simply add your configuration into the container::

    <?php
    // File "/index.php".

    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/DependencyInjectionContainer.php';
    require_once dirname(__FILE__) . './Configuration/LinkedLetters.php';
    require_once dirname(__FILE__) . './Configuration/LetterTypes.php';

    define('MINIMUM_LENGTH', 5);
    define('MAXIMUM_LENGTH', 11);

    $length = rand(MINIMUM_LENGTH, MAXIMUM_LENGTH);

    $container = new PronounceableWord_DependencyInjectionContainer();
    $container->configurations['LinkedLetters'] = new My_Configuration_LinkedLetters();
    $container->configurations['LetterTypes'] = new My_Configuration_LetterTypes();

    $generator = $container->getGenerator();
    $password = $generator->generateWordOfGivenLength($length);

Conclusion
==========

You now have a pronounceable password generator. If you want to use upper and
lower case, you should use a function aferwards::

    <?php
    // File "./addUppercase.php"
    define('CHOOSE_LOWER_CASE', 0);
    define('CHOOSE_UPPER_CASE', 1);

    function addUppercase($word) {
        $maximumLetterIndex = strlen($word);
        for ($letterIndex = 0; $letterIndex < $maximumLetterIndex, $letterIndex++) {
            $choice = rand(CHOOSE_LOWER_CASE, CHOOSE_UPPER_CASE);
            if (CHOOSE_UPPER_CASE === $choice) {
                $word[$letterIndex] = strtoupper($word[$letterIndex]);
            }
        }

        return $word;
    }

And::

    <?php
    // File "/index.php".

    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/DependencyInjectionContainer.php';
    require_once dirname(__FILE__) . './Configuration/LinkedLetters.php';
    require_once dirname(__FILE__) . './Configuration/LetterTypes.php';

    define('MINIMUM_LENGTH', 5);
    define('MAXIMUM_LENGTH', 11);

    $length = rand(MINIMUM_LENGTH, MAXIMUM_LENGTH);

    $container = new PronounceableWord_DependencyInjectionContainer();
    $container->configurations['LinkedLetters'] = new My_Configuration_LinkedLetters();
    $container->configurations['LetterTypes'] = new My_Configuration_LetterTypes();

    $generator = $container->getGenerator();
    $password = $generator->generateWordOfGivenLength($length);

    $password = addUppercase($password);
