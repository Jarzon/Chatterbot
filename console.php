<?php
/**
 * A scrapper for AIML documents
 */

use Chatterbot\BasePack\Service\Container;

$root = dirname(__FILE__);

$config = include("$root/app/config/config.php");

$config = array_merge($config, [
    'root' => $root,
    'app' => "{$root}/app"
]);

// Composer autoloading
require  "$root/vendor/autoload.php";

$container = new Container(include("$root/app/config/container.php"), $config);

$xml = simplexml_load_file("$root/data/aiml/ai.aiml");

$pattern = '';

/** @var Chatterbot\ChatterbotPack\Model\SentenceModel $model */
$model = $container->getModel('Chatterbot\ChatterbotPack\Model\SentenceModel');
$sentenceHelper = $container->getSentenceHelper();

foreach ($xml as $key => $value) { // Category tag

    foreach ($value as $key => $value) { // Sub tags (pattern, template, ...)
        if($key === 'pattern') {
            $pattern = $value;
        }
        else if($key === 'template' && !is_array($value) && !empty($pattern)) {

            $words = $sentenceHelper->getWords($value);
            foreach($words as $word) {
                if($word != null) {
                    $wordRes = $model->getWord($word);

                    if($wordRes) {
                        $wordId = $wordRes->word_id;
                    } else {
                        $wordId = $model->addWord($word);
                    }

                    $words_list[] = ['id' => $wordId, 'weight' => $this->sentenceHelper->getWordWeight($word)];
                }
            }

            $sentenceId = $model->addSentence($_POST['response']);
            $lastId = $model->getConnectionLastId();
            $lastId = $lastId->last_id;

            $connectionId = ($lastId + 1);

            foreach($words_list as $word) {
                $model->addConnection($connectionId, $word['id'], $sentenceId, $word['weight']);
            }
        } else {
            $pattern = '';
        }
    }
}