<?php

namespace LogParser;

use Aerys\Request;
use Aerys\Response;

function getLogPath(): string
{
    $iniFileLog = ini_get('error_log');
    if (strlen($iniFileLog) > 0 && is_file($iniFileLog))
    {
        return $iniFileLog;
    }
    $envFilePath = getenv('PHP_ERROR_LOG');
    if (strlen($envFilePath) > 0 && is_file($envFilePath))
    {
        return $envFilePath;
    }
    throw new UndefinedErrorLog;
}

/**
 * Route declared in router would be /lines/{file}/{line_range}
 * where line_range would be in the form, LINE_NO_START:LINE_NO_END
 * @param Request $request
 * @param Response $response
 * @param array $args
 */
function getLinesOfInterest(Request $request, Response $response, array $args): void {
    $file = trim($args['file']);
    $lineRange = explode(':', trim($args['line_range']));

    $response->setHeader('Content-Type', 'application/json');

    if(count($lineRange) === 1) {
        // If the range is malformed, send a 500
        $response
            ->setStatus(500)
            ->end(json_encode([
                "code" => 500,
                "message" => "Line Range value is malformed"
            ]));
        return;
    }

    // Check if the file exists
    if(!file_exists($file)) {
        // If not, send a failure 404 response.
        $response
            ->setStatus(404)
            ->end(json_encode([
                "code" => 404,
                "message" => "The file which generated this error is not present anymore"
            ]));
        return;
    }

    // Get file content
    $fileContent = file_get_contents($file);

    // Explode into lines
    $fileLines = explode(PHP_EOL, $fileContent);

    $lineRange1 = (int) $lineRange[0];
    $lineRange2 = (int) $lineRange[1];

    // Check if the range exists
    if(!isset($fileLines[$lineRange1]) && !isset($fileLines[$lineRange2])) {
        // If not, send a 404 error
        $response
            ->setStatus(404)
            ->end(json_encode([
                "code" => 404,
                "message" => "The lines which generated these error are not present anymore"
            ]));
        return;
    }

    // If it passes every check, now loop through the specified range
    // and keep appending to an array
    $linesToSend = [];
    for($i = $lineRange1; $i <= $lineRange2; $i++) {
        $linesToSend[] = $fileLines[$i];
    }

    // Ship it!
    $response
        ->setStatus(200)
        ->end(json_encode([
            "code" => 200,
            "message" => "OK",
            "lines" => $linesToSend
        ]));
    return;
}

function getFileContent($fileName) {
    return htmlspecialchars(file_get_contents($fileName));
}
