<?php
use Chatterbot\BasePack\Service\Container;

$root = __DIR__ . DIRECTORY_SEPARATOR;

$config = [
    'root' => $root,
    'app' => "{$root}app/"
];

// Composer autoloading
require "{$config['root']}vendor/autoload.php";

$config = (include("{$config['app']}config/config.php")) + $config;

$container = new Container(include("{$config['app']}/config/container.php"), $config);

$db = null;

if($config['db_enable']) {
    $db = $container->getPDO($config['db_type'], $config['db_host'], $config['db_name'], $config['db_charset'], $config['db_user'], $config['db_password'], $config['db_options']);
}

$controller = $container->getController('Chatterbot\BasePack\Controller\Home');

$controller->discord();