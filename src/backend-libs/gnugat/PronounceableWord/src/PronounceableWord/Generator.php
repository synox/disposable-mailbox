<?php
/*
 * This file is part of the PronounceableWord library.
 *
 * (c) Loic Chardonnet
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

class PronounceableWord_Generator {
    protected $word;
    protected $length;

    protected $linkedLetters;
    protected $letterTypes;
    protected $lastLettersConsecutiveTypes;
    protected $configuration;

    public function __construct($linkedLetter, $letterTypes, $lastLettersConsecutiveTypes, $configuration) {
        $this->linkedLetters = $linkedLetter;
        $this->letterTypes = $letterTypes;
        $this->lastLettersConsecutiveTypes = $lastLettersConsecutiveTypes;
        $this->configuration = $configuration;
    }

    public function generateWordOfGivenLength($givenLength) {
        $this->word = '';
        $this->length = 0;
        for ($letterNumber = 0; $letterNumber < $givenLength; $letterNumber++) {
            $this->word .= $this->pickNextLetter();
            $this->length = strlen($this->word);
        }

        return $this->word;
    }

    protected function pickNextLetter() {
        $pickedLetter = '';

        if (0 === $this->length) {
            $pickedLetter = $this->pickFirstLetter();
        } elseif ($this->length <= $this->configuration->maximumConsecutiveTypesAtTheBegining) {
            $pickedLetter = $this->pickLinkedLetterOfDifferentType($this->word[0]);
        } else {
            $pickedLetter = $this->pickLinkedLetterOfDifferentTypeIfLastLettersAreOfConsecutiveTypes();
        }

        return $pickedLetter;
    }

    protected function pickFirstLetter() {
        return $this->linkedLetters->pickLetter();
    }

    protected function pickLinkedLetterOfDifferentType($letter) {
        $letterType = $this->letterTypes->getLetterType($letter);
        $lettersToAvoid = $this->letterTypes->getLettersOfGivenType($letterType);

        return $this->linkedLetters->pickLinkedLetterDifferentFromGivenLetters($letter, $lettersToAvoid);
    }

    protected function pickLinkedLetterOfDifferentTypeIfLastLettersAreOfConsecutiveTypes() {
        $lastLetter = $this->word[$this->length - 1];
        $consecutiveTypes = $this->lastLettersConsecutiveTypes->countFromWord($this->word);

        $pickedLetter = '';
        if ($consecutiveTypes === $this->configuration->maximumConsecutiveTypesInTheWord) {
            $pickedLetter = $this->pickLinkedLetterOfDifferentType($lastLetter);
        } else {
            $pickedLetter = $this->linkedLetters->pickLinkedLetter($lastLetter);
        }

        return $pickedLetter;
    }
}
