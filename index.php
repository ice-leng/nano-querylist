<?php

date_default_timezone_set("Asia/Shanghai");

require_once __DIR__ . '/vendor/autoload.php';
$app = Hyperf\Nano\Factory\AppFactory::create();

$dotenv = \Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

$configs = include __DIR__ . '/config.php';
$app->config($configs);

$app->run();
