<?php
$ds = DIRECTORY_SEPARATOR;
require_once __DIR__."/../vendor/composer/ClassLoader.php";

$appPath = realpath(__DIR__ . $ds . ".." . $ds . "src");
$generatedPath = realpath(__DIR__ . $ds . ".." . $ds . "gen");

function registerNamespace($namespace, $path) {
	$classLoader = new \Composer\Autoload\ClassLoader();
	$classLoader->add($namespace, $path);
	$classLoader->register(true);
}

registerNamespace('Controller', $appPath);
registerNamespace('Factory', $appPath);
registerNamespace('Model', $appPath);
registerNamespace('Exception', $appPath);
registerNamespace('Util', $appPath);
registerNamespace('Service', $appPath);
registerNamespace('Generated', $generatedPath);
registerNamespace('Helpers', $appPath);
registerNamespace('Listeners', $appPath);