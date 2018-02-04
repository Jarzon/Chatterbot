<?php
namespace Chatterbot\ChatterbotPack\Service;

class SentenceHelper
{
    public function getWords($question) : array {
        $question = strtolower($question);

        $question = str_replace(['"', '\'', '.', '!', '?'], '', $question);

        return explode(' ', $question);
    }

    public function getWordWeight($word) : int {
        $commonWords = [
            'he', 'and', 'a', 'to', 'is', 'you', 'that', 'it', 'he', 'for', 'as', 'with', 'his', 'they', 'I', 'at', 'this', 'or', 'one', 'by', 'but', 'not', 'what', 'we', 'an', 'your', 'she', 'her', 'him', 'their', 'if', 'there', 'out', 'them', 'these', 'so', 'my', 'than', 'its', 'us'
        ];

        $weight = 2;

        if(in_array($word, $commonWords)) $weight--;

        return $weight;
    }
}