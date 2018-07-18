<?php
/** @var $this Prim\Router */
$this->get('/', 'ChatterbotPack\Backend', 'index');

$this->addGroup('/admin', function(Prim\Router $r) {
    $r->addRoute(['GET', 'POST'], '/login', 'ChatterbotPack\Backend', 'login');

    $r->addRoute(['GET', 'POST'], '/[{page:\d+}]', 'ChatterbotPack\Backend', 'index');

    $r->addRoute(['GET', 'POST'], '/edit/{sentence:\d+}', 'ChatterbotPack\Backend', 'editQuestion');

    $r->get('/delete/{sentence:\d+}', 'ChatterbotPack\Backend', 'deleteSentence');
});