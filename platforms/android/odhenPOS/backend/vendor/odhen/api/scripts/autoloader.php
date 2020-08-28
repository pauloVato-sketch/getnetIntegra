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
if (!function_exists('getallheaders'))
{
    function getallheaders()
    {
           $headers = '';
       foreach ($_SERVER as $name => $value)
       {
           if (substr($name, 0, 5) == 'HTTP_')
           {
               $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
           }
       }
       return $headers;
    }
}

registerNamespace('Controller', $appPath);
registerNamespace('Factory', $appPath);
registerNamespace('Model', $appPath);
registerNamespace('Exception', $appPath);
registerNamespace('Util', $appPath);
registerNamespace('Service', $appPath);
registerNamespace('Test', $appPath);
registerNamespace('Generated', $generatedPath);