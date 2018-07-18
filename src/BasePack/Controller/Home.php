<?php
namespace Chatterbot\BasePack\Controller;

use Prim\Controller;

class Home extends Controller
{
    /**
     * @var \Chatterbot\ChatterbotPack\Service\SentenceHelper $sentenceHelper
     * @var \Chatterbot\ChatterbotPack\Model\SentenceModel $model
     */
    public $sentenceHelper;
    public $model;

    function build() {
        $this->sentenceHelper = $this->container->getSentenceHelper();
    }

    public function discord()
    {
        $this->model = $this->getModel('SentenceModel', 'ChatterbotPack');

        $discord = new \Discord\Discord([
            'token' => $this->options['discord_token']
        ]);

        $discord->on('ready', function ($discord) {
            echo "Bot is ready.", PHP_EOL;

            // Listen for events here
            $discord->on('message', function ($message) {
                if($message->author->id !== '468512593708056596' && strpos($message->content, "@{$this->options['discord_bot_name']}")) {
                    echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;

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
