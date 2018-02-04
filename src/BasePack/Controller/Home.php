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
}
