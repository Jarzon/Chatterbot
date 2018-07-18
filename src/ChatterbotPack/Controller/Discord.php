<?php
namespace Chatterbot\ChatterbotPack\Controller;

use Prim\Controller;

class Discord extends Controller
{
    /** @var \Chatterbot\ChatterbotPack\Service\SentenceHelper $sentenceHelper */
    public $sentenceHelper;
    /** @var \Chatterbot\ChatterbotPack\Model\SentenceModel $model */
    public $model;

    function build() {
        $this->sentenceHelper = $this->container->getSentenceHelper();
    }

    public function run()
    {
        $this->model = $this->getModel('SentenceModel', 'ChatterbotPack');

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

                    $words = $this->sentenceHelper->getWords($message->content);

                    $response = $this->model->getResponse($words);

                    if($response) {
                        $message->channel->sendMessage($response[0]->sentence);
                    }
                }
            });
        });

        $discord->run();
    }
}
