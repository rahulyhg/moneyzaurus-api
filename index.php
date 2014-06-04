<?php

define('APP_DEV', (bool)getenv('APP_DEV'));


error_reporting(E_ALL);
ini_set('display_errors', intval(APP_DEV));

require 'vendor/autoload.php';


$configFile = get_cfg_var('app_config_file');
$configFile = !$configFile ? 'config/config.php' : $configFile;

$configData = include __DIR__ . DIRECTORY_SEPARATOR . $configFile;


$config    = new Api\Module\Config();
$container = new Api\Module\Container();

try {
    $container
        ->setConfig($config->setConfig($configData))
        ->getModule()
        ->run();

} catch (\Exception $exc) {
    /** @var \Monolog\Logger $logger */
    $logger = $container->get(Api\Module\Container::LOGGER);
    $logger->addError($exc);

    header('HTTP/1.1 500 Internal Server Error');
}