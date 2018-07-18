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

                $isMentioned = $this->isMentioned($message);

                if($this->isNotSelfMessage($message) && ($isMentioned || $this->isPrivateChanel($message))) {
                    if($isMentioned) {
                        $message->content = $this->removeMention($message);
                    }

                    $response = $this->model->getResponse($message->content);

                    if($response) {
                        // TODO: Answer a random response If there is more that one
                        $message->channel->sendMessage($response[0]->sentence);
                    }
                }
            });
        });

        $discord->run();
    }

    protected function isNotSelfMessage($message)
    {
        return $message->author->id !== $this->options['discord_bot_id'];
    }

    protected function isMentioned($message)
    {
        return strpos($message->content, "<@{$this->options['discord_bot_id']}>");
    }

    protected function removeMention($message)
    {
        return str_replace("<@{$this->options['discord_bot_id']}>", '', $message->content);
    }

    protected function isPrivateChanel($message)
    {
        return $message->author->guild_id === null;
    }
}
