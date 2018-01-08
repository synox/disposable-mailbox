Configuration
=============

To generate pronounceable words, **PronouceableWord** uses three types of
configuration:

================ ================================ ================================================
Configuration    What it defines                  What it allows
================ ================================ ================================================
Linked letters   The link between letters         To know which letters can follow a letter
Letter types     The types of the letters         To alternate between consonants, vowels, etc...
Generator        The number of consecutive types  To know when to alternate the types
================ ================================ ================================================

PronouceableWord_Configuration_LinkedLetters
--------------------------------------------

This class contains only one attribute (``lettersWithLinkedLetters``), which
is an associative array:

Key
  A string of only one character representing the letter.

Value
  A string representing the letters supposed to follow well the **key**.

For example, having ``'b' => 'a'`` will allow to compose ``'ba'``.

PronouceableWord_Configuration_LetterTypes
------------------------------------------

This class contains only one attribute (``letterTypesWithLetters``), which is
an associative array:

Key
  The name of the type.

Value
  A string containing the letters of this type.

For example, defining the vowels would be: ``'vowels' => 'aeiouy'``.

PronounceableWord_Configuration_Generator
-----------------------------------------

This class contains two attributes which must be integers superior to 1.:

===================================== ==============================================================================
Attribute                             Example value and effect
===================================== ==============================================================================
maximumConsecutiveTypesAtTheBegining  1: if the first letter is a vowel, the second will be a consonant
maximumConsecutiveTypesInTheWord      2: if the second and third letters are vowels, the fourth will be a consonant
===================================== ==============================================================================

Default configuration
=====================

The default configuration provides a set of linked letters based on the study
of Data Compression (http://www.data-compression.com/english.shtml#second),
using there statistical study of English text.

Some changes have been applied:

* only letters with a probability superior to 0.01 have been selected;
* the letters 'j' and 'q' have been removed.

How to customize the configuration
==================================

To customize the configuration, you need to:

1. copy the configuration classes in
   ``./vendor/PronounceableWord/src/PronounceableWord/Configuration`` to your
   project configuration (e.g. ``./Configuration``);
2. change the name of your configuration classes to avoid confusion, (e.g.
   replacing the prefix ``PronounceableWord`` by ``My``);
3. change the content as you wish;
4. set the configuration attributes in the dependency injection container
   with your own classes.

How to test the configuration
=============================

To make sure your customized configuration is coherent and won't make
**PronounceableWord** crash, you can test it as follow:

1. create a unit test extending the configuration test (from
   ``./vendor/PronounceableWord/test/PronounceableWord/Tests/Configuration``);
2. override the ``setUp`` method by initializing the ``configuration``
   attribute with your own configuration class.

To learn more about how to test, see ``./doc/tests.srt``.

Full Example
============

Here is a complete example, to show how it works.

Customizing
-----------

The configuration::

    <?php
    // File copied: "./vendor/PronounceableWord/src/PronounceableWord/Configuration/LinkedLetters.php"
    // into: "./Configuration/LinkedLetters.php"
    
    class My_Configuration_LinkedLetters {
        public $lettersWithLinkedLetters = array(
            'a' => 'bc',
            'b' => 'ac',
            'c' => 'a0',
            '0' => 'abc',
        );
    }

    <?php
    // File copied: "./vendor/PronounceableWord/src/PronounceableWord/Configuration/LetterTypes.php"
    // into: "./Configuration/LetterTypes.php"

    class My_Configuration_LetterTypes {
        public $letterTypesWithLetters = array(
            'vowels' => 'a',
            'consonants' => 'bc',
            'numbers' => '0',
        );
    }

    <?php
    // File copied: "./vendor/PronounceableWord/src/PronounceableWord/Configuration/Generator.php"
    // into: "./Configuration/Generator.php"

    class My_Configuration_Generator {
        public $maximumConsecutiveTypesAtTheBegining = 1;
        public $maximumConsecutiveTypesInTheWord = 2;
    }

This configuration is fine:

* each letters have at least one linked letters of a different type;
* there are at least two different types;
* every letters are present in the letter types;
* the number of consecutive types are strictly positives.

Usage
-----

To use it, just set them into the container::

    <?php
    // File "/index.php".

    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/DependencyInjectionContainer.php';
    require_once dirname(__FILE__) . '/Configuration/LinkedLetters.php';
    require_once dirname(__FILE__) . '/Configuration/LetterTypes.php';
    require_once dirname(__FILE__) . '/Configuration/Generator.php';

    define('MINIMUM_LENGTH', 5);
    define('MAXIMUM_LENGTH', 11);

    $length = rand(MINIMUM_LENGTH, MAXIMUM_LENGTH);

    $container = new PronounceableWord_DependencyInjectionContainer();
    $container->configurations['LinkedLetters'] = new My_Configuration_LinkedLetters();
    $container->configurations['LetterTypes'] = new My_Configuration_LetterTypes();
    $container->configurations['Generator'] = new My_Configuration_Generator();

    $generator = $container->getGenerator();
    $word = $generator->generateWordOfGivenLength($length);

Testing
-------

To test it, create the following unit tests::

    <?php
    // File /test/Configuration/LinkedLettersTest.php

    require_once dirname(__FILE__) . '/../../vendor/PronounceableWord/test/PronounceableWord/Tests/Configuration/LinkedLettersTest.php';
    require_once dirname(__FILE__) . '/../../Configuration/LinkedLetters.php';

    class My_Tests_Configuration_LinkedLettersTest extends PronounceableWord_Tests_Configuration_LinkedLettersTest {
        public function setUp() {
            $this->configuration = new PronounceableWord_Configuration_LinkedLetters();
        }
    }

    <?php
    // File /test/Configuration/LetterTypesTest.php

    require_once dirname(__FILE__) . '/../../vendor/PronounceableWord/test/PronounceableWord/Tests/Configuration/LetterTypesTest.php';
    require_once dirname(__FILE__) . '/../../Configuration/LetterTypes.php';

    class My_Tests_Configuration_LetterTypesTest extends PronounceableWord_Tests_Configuration_LetterTypesTest {
        public function setUp() {
            $this->configuration = new PronounceableWord_Configuration_LetterTypes();
        }
    }

    <?php
    // File /test/Configuration/GeneratorTest.php

    require_once dirname(__FILE__) . '/../../vendor/PronounceableWord/test/PronounceableWord/Tests/Configuration/GeneratorTest.php';
    require_once dirname(__FILE__) . '/../../Configuration/Generator.php';

    class My_Tests_Configuration_GeneratorTest extends PronounceableWord_Tests_Configuration_GeneratorTest {
        public function setUp() {
            $this->configuration = new PronounceableWord_Configuration_Generator();
        }
    }
