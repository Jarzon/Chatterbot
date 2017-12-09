<?php
$this->addGroup('/admin', function($r) {
    $r->get('/[{page:\d+}]', 'ChatterbotPack\Home', 'index');
    $r->post('/', 'ChatterbotPack\Home', 'addSentence');

    $r->addRoute(['GET', 'POST'], '/edit/{sentence:\d+}', 'ChatterbotPack\Home', 'editQuestion');

    $r->get('/delete/{sentence:\d+}', 'ChatterbotPack\Home', 'deleteSentence');
});