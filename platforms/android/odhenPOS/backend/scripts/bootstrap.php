<?php
require "autoloader.php";
require __DIR__."/../vendor/autoload.php";
require __DIR__."/../vendor/zeedhi/framework/bootstrap.php";

// Load specified services and parameters
$instanceManager = \Zeedhi\Framework\DependencyInjection\InstanceManager::getInstance();
$instanceManager->loadFromFile('../environment.xml');
$instanceManager->loadFromFile('../parametrizacao.xml');
$instanceManager->loadFromFile('../vendor/odhen/api/services.xml');
$instanceManager->loadFromFile('../services.xml');
$instanceManager->compile();