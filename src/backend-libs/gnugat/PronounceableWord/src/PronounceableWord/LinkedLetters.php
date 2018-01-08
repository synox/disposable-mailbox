<?php
/*
 * This file is part of the PronounceableWord library.
 *
 * (c) Loic Chardonnet
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

class PronounceableWord_LinkedLetters {
    protected $configuration;

    public function  __construct($configuration) {
        $this->configuration = $configuration;
    }
    public function pickLetter() {
        $pickedLetter = array_rand($this->configuration->lettersWithLinkedLetters);

        return $pickedLetter;
    }

    public function pickLinkedLetter($letter) {
        $linkedLetters = $this->configuration->lettersWithLinkedLetters[$letter];

        return $this->pickLetterFromGivenLetters($linkedLetters);
    }

    protected function pickLetterFromGivenLetters($letters) {
        $minLetterIndex = 0;
        $maxLetterIndex = strlen($letters) - 1;
        $pickedLetterIndex = rand($minLetterIndex, $maxLetterIndex);

        $pickedLetter = $letters[$pickedLetterIndex];

        return $pickedLetter;
    }

    public function pickLinkedLetterDifferentFromGivenLetters($letter, $letters) {
        $linkedLetters = $this->configuration->lettersWithLinkedLetters[$letter];

        $letterChoices = $this->removeGivenLettersFromGivenLinkedLetters($letters, $linkedLetters);

        return $this->pickLetterFromGivenLetters($letterChoices);
    }

    protected function removeGivenLettersFromGivenLinkedLetters($letters, $linkedLetters) {
        $maximumLetterIndex = strlen($letters);
        for ($letterIndex = 0; $letterIndex < $maximumLetterIndex; $letterIndex++) {
            $linkedLetters = str_replace($letters[$letterIndex], '', $linkedLetters);
        }

        return $linkedLetters;
    }
}
