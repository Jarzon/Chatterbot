<?php
/** @var $this Prim\Router */
$this->get('/login', 'ChatterbotPack\Home', 'index');

$this->addGroup('/admin', function(Prim\Router $r) {
    $r->both('/login', 'ChatterbotPack\Home', 'login');

    $r->get('/[{page:\d+}]', 'ChatterbotPack\Home', 'index');
    $r->post('/', 'ChatterbotPack\Home', 'addSentence');

    $r->addRoute(['GET', 'POST'], '/edit/{sentence:\d+}', 'ChatterbotPack\Home', 'editQuestion');

    $r->get('/delete/{sentence:\d+}', 'ChatterbotPack\Home', 'deleteSentence');
});