<?php
/*
 * This file is part of the PronounceableWord library.
 *
 * (c) Loic Chardonnet
 *
 * For the full copyright and license information, please view the LICENSE.txt
 * file that was distributed with this source code.
 */

class PronounceableWord_LetterTypes {
    protected $configuration;

    public function  __construct($configuration) {
        $this->configuration = $configuration;
    }

    public function getLetterType($letter) {
        $type = '';
        foreach ($this->configuration->letterTypesWithLetters as $letterType => $letters) {
            if (false !== strpos($letters, $letter)) {
                $type = $letterType;
                break;
            }
        }

        return $type;
    }

    public function getLettersOfGivenType($type) {
        return $this->configuration->letterTypesWithLetters[$type];
    }
}
