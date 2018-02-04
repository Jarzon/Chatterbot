<?php
use Prim\Container;

session_start();

$root = dirname(__DIR__) . DIRECTORY_SEPARATOR;

$config = [
    'root' => $root,
    'app' => "{$root}app/"
];

$config = (include("{$config['app']}config/config.php")) + $config;

// Composer autoloading
require  "{$config['root']}vendor/autoload.php";

$container = new Container(include("{$config['app']}config/container.php"), $config);

function rmspaces($buffer){
    return preg_replace('~^([ \t\n]+)~m', '', $buffer);
};

ob_start("rmspaces");
$container->getApplication();
ob_end_flush();