<?php $this->start('default') ?>
    <h1><?=$_('page not found')?></h1>

    <p><?=$_('url not found')?><?=$_SERVER['REQUEST_URI']?></p>
<?php $this->end() ?>