<?php
include_once __DIR__ . "/vendor/autoload.php";

$logFile = new \LogParser\ErrorLogFile("/home/saitama/php_error.log");

$parser = new \LogParser\Parser($logFile);
$parser->start();

$logs = $parser->getLogs();

$jsonify = new \LogParser\JSONify($logs);
echo $jsonify->convert()->getJSON();