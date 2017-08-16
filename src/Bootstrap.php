<?php

$router = \Aerys\router()
->route('GET', '/logs.json', function(\Aerys\Request $request, \Aerys\Response $response) {
    $logFile = new \LogParser\ErrorLogFile(\LogParser\getLogPath());

    $parser = new \LogParser\Parser($logFile);
    $parser->start();

    $logs = $parser->getLogs();

    $jsonify = new \LogParser\JSONify($logs);
    $response
        ->setHeader("Content-Type", "application/json")
        ->end($jsonify->convert()->getJSON());
})
->route('GET', '/lines/{file}/{line_range}', function (\Aerys\Request $request, \Aerys\Response $response, array $args) {
    $args['file'] = str_replace('_', '/', $args['file']);
    \LogParser\getLinesOfInterest($request, $response, $args);
});

(new \Aerys\Host())
    ->expose("*", 2048)
    ->use($router)
    ->use(\Aerys\root(__DIR__ . '/../public'));
