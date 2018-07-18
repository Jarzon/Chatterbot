<?php
namespace Chatterbot\Controller;

class Discord
{
    /** @var \Chatterbot\Model\SentenceModel $model */
    public $model;

    public function __construct($model, $options)
    {
        $this->model = $model;
        $this->options = $options;
    }

    public function run()
    {
        $discord = new \Discord\Discord([
            'token' => $this->options['discord_token']
        ]);

        $discord->on('ready', function ($discord) {
            echo "Bot is ready.", PHP_EOL;

            // Listen for events here
            $discord->on('message', function ($message) {
                echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;

                if($message->author->id !== $this->options['discord_bot_id'] && strpos($message->content, "<@{$this->options['discord_bot_id']}>")) {
                    $message->content = str_replace("<@{$this->options['discord_bot_id']}>", '', $message->content);

                    $words = $this->getWords($message->content);

                    $response = $this->model->getResponse($words);

                    if($response) {
                        // TODO: Answer a random response If there is more that one
                        $message->channel->sendMessage($response[0]->sentence);
                    }
                }
            });
        });

        $discord->run();
    }

    protected function getWords($question) : array {
        $question = strtolower($question);

        $question = str_replace(['"', '\'', '.', '!', '?'], '', $question);

        return explode(' ', $question);
    }

    protected function getWordWeight($word) : int {
        $weight = 2;

        // List of common words that are used too often
        $commonWords = [
            'he', 'and', 'a', 'to', 'is', 'you', 'that', 'it', 'he', 'for', 'as', 'with', 'his', 'they', 'I', 'at', 'this', 'or', 'one', 'by', 'but', 'not', 'what', 'we', 'an', 'your', 'she', 'her', 'him', 'their', 'if', 'there', 'out', 'them', 'these', 'so', 'my', 'than', 'its', 'us'
        ];

        if(in_array($word, $commonWords)) $weight--;

        return $weight;
    }
}
