<?php
namespace Chatterbot\BasePack\Controller;

use Prim\Controller;

/**
 * Class Home
 *
 */
class Home extends Controller
{
    /**
     * PAGE: index
     */
    public function index()
    {
        $model = $this->getModel('BaseModel');

        $this->addVar('name', 'anonymous');

        $this->render('home/index');
    }
}
