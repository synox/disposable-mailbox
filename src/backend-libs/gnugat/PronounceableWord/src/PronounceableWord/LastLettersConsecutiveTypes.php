<?php
/*
 * This file is part of the PronounceableWord library.
 *
 * (c) Loic Chardonnet
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

class PronounceableWord_LastLettersConsecutiveTypes {
    protected $letterTypes;

    public function __construct($letterTypes) {
        $this->letterTypes = $letterTypes;
    }

    public function countFromWord($word) {
        $letterIndex = strlen($word) - 1;
        $letter = $word[$letterIndex];
        $type = $this->letterTypes->getLetterType($letter);
        $consecutiveTypesCount = 1;

        for ($letterIndex = $letterIndex - 1; $letterIndex >= 0; $letterIndex--) {
            $letter = $word[$letterIndex];
            if ($this->letterTypes->getLetterType($letter) !== $type) {
                break;
            }
            $consecutiveTypesCount++;
        }

        return $consecutiveTypesCount;
    }
}
