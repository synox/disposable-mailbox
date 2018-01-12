Offensive and insulting words management
========================================

Because PronounceableWord uses an algorithm using randomness,
offensive or insulting words might be generated. The best way to manage them
is to ceate a function that will filter them afterward.

These offensive words might be surrounded by other letters that would be fine
otherwise, so you should replace it by a non-offensive one instead of just
removing it.

To avoid generating unpronounceable words with this filter, try to be
consistent with the configuration.

Example
-------

Here is an example on how to manage them. With the default configuration, the
word "insult" can be generated. Let's replace it by "insalt": the letter "s"
has "a" as a linked letter, and the letter "a" has "l" has a linked letter::

    <?php
    // File "/OffensiveAndInsultingWords.php".

    class OffensiveAndInsultingWords {
        protected $offensiveAndInsultingWords = array(
            'insult' => 'insalt',
        );

        public function filter($word) {
            foreach ($this->offensiveAndInsultingWords as $offensiveAndInsultingWord => $replacement) {
                $word = str_replace($offensiveAndInsultingWord, $replacement, $word);
            }

            return $word;
        }
    }

Now, after generating your words with ``PronounceableWord_Generator``, you can
use ``OffensiveAndInsultingWords`` to filter any words you might find offensive
or insulting::

    <?php
    // File "/index.php".

    require_once dirname(__FILE__) . '/OffensiveAndInsultingWords.php';
    require_once dirname(__FILE__) . '/vendor/PronounceableWord/src/PronounceableWord/DependencyInjectionContainer.php';

    define('MINIMUM_LENGTH', 5);
    define('MAXIMUM_LENGTH', 11);

    $length = rand(MINIMUM_LENGTH, MAXIMUM_LENGTH);

    $container = new PronounceableWord_DependencyInjectionContainer();
    $generator = $container->getGenerator();
    $offensiveAndInsultingWordManager = new OffensiveAndInsultingWords();

    $word = $generator->generateWordOfGivenLength($length);
    $word = offensiveAndInsultingWordManager->filter($word);
