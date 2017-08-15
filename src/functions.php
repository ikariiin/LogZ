<?php

namespace LogParser;

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
