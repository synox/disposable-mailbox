<?php
/*
 * This file is part of the PronounceableWord library.
 *
 * (c) Loic Chardonnet
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

require_once dirname(__FILE__) . '/../../../src/PronounceableWord/Configuration/LinkedLetters.php';
require_once dirname(__FILE__) . '/../../../src/PronounceableWord/LinkedLetters.php';

class PronounceableWord_Tests_LinkedLettersTest extends PHPUnit_Framework_TestCase {
    public function testPickLetter() {
        $configuration = new PronounceableWord_Configuration_LinkedLetters();
        $linkedLetters = new PronounceableWord_LinkedLetters($configuration);

        $maximumTestNumber = 1000;
        for ($currentTestNumber = 0; $currentTestNumber < $maximumTestNumber; $currentTestNumber++) {
            $chosenLetter = $linkedLetters->pickLetter();

            $this->assertArrayHasKey($chosenLetter, $configuration->lettersWithLinkedLetters);
        }
    }

    public function testPickLinkedLetter() {
        $configuration = new PronounceableWord_Configuration_LinkedLetters();
        $linkedLetters = new PronounceableWord_LinkedLetters($configuration);

        $maximumTestNumber = 1000;
        foreach ($configuration->lettersWithLinkedLetters as $currentLetter => $currentLinkedLetters) {
            for ($currentTestNumber = 0; $currentTestNumber < $maximumTestNumber; $currentTestNumber++) {
                $chosenLinkedLetter = $linkedLetters->pickLinkedLetter($currentLetter);

                $isChosenLetterInLinkedLetters = strpos($currentLinkedLetters, $chosenLinkedLetter);

                $this->assertNotEquals(false, $isChosenLetterInLinkedLetters);
            }
        }
    }

    public function testPickLinkedLetterDifferentFromGivenLetters() {
        $configuration = new PronounceableWord_Configuration_LinkedLetters();
        $linkedLetters = new PronounceableWord_LinkedLetters($configuration);

        $maximumTestNumber = 1000;
        foreach ($configuration->lettersWithLinkedLetters as $currentLetter => $currentLinkedLetters) {
            for ($currentTestNumber = 0; $currentTestNumber < $maximumTestNumber; $currentTestNumber++) {
                $chosenLinkedLetter = $linkedLetters->pickLinkedLetterDifferentFromGivenLetters($currentLetter, $currentLetter);

                $isChosenLetterInLinkedLetters = strpos($currentLinkedLetters, $chosenLinkedLetter);

                $isChosenLetterDifferentThanCurrentLetter = false;
                if ($chosenLinkedLetter !== $currentLetter) {
                    $isChosenLetterDifferentThanCurrentLetter = true;
                }

                $isChosenLetterValid = false;
                if (false !== $isChosenLetterInLinkedLetters && true === $isChosenLetterDifferentThanCurrentLetter) {
                    $isChosenLetterValid = true;
                }

                $this->assertTrue($isChosenLetterValid);
            }
        }
    }
}
