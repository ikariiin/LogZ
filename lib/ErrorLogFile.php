<?php
namespace LogParser;

use LogParser\Streams\FileStream;

/**
 * Entry point for the the log file.
 * @package LogParser
 */
class ErrorLogFile {
    private $path;

    public function __construct(string $path) {
        $this->path = $path;
    }

    public function load(): FileStream {
        return new FileStream($this->path);
    }
}