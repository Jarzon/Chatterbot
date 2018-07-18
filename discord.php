<?php
$root = __DIR__ . DIRECTORY_SEPARATOR;

$config = [
    'root' => $root
];

$config = (include("{$config['root']}config/config.php")) + $config;

require "{$config['root']}vendor/autoload.php";

$model = new Chatterbot\Model\SentenceModel($config);

$controller = new Chatterbot\Controller\Discord($model, $config);

$controller->run();