<?php
global $startTime;
$startTime= microtime(true);

require_once '../scripts/bootstrap.php';
/** @var \Zeedhi\Framework\Application $application */
$application = $instanceManager->getService('application');
$application->run();
die;