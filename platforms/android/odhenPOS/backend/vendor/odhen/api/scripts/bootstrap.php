<?php
require "autoloader.php";
require __DIR__."/../vendor/autoload.php";
require __DIR__."/../vendor/zeedhi/framework/bootstrap.php";

// Load specified services and parameters
$instanceManager = \Zeedhi\Framework\DependencyInjection\InstanceManager::getInstance();
$instanceManager->loadFromFile('../services.xml');
$instanceManager->loadFromFile('../environment.xml');
$instanceManager->compile();