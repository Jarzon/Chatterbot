<?php
namespace Chatterbot\BasePack\Controller;

use Prim\Controller;

class Home extends Controller
{
    public function index()
    {
        $this->addVar('name', 'anonymous');

        $this->render('home/index');
    }

    public function discord()
    {
        $discord = new \Discord\Discord([
            'token' => 'NDY4NTEyNTkzNzA4MDU2NTk2.DjApyA.TRct1SRGcuzxyRtfuTI9qnViUP4',
            'name' => 'Botty'
        ]);

        $discord->on('ready', function ($discord) {
            echo "Bot is ready.", PHP_EOL;

            // Listen for events here
            $discord->on('message', function ($message) {
                echo "Recieved a message from {$message->author->username}: {$message->content}", PHP_EOL;
            });
        });

        $discord->run();
    }
}
