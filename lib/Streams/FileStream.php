<?php
namespace LogParser\Streams;

class FileStream {
    private $stream;

    /**
     * FileStream constructor.
     * @param string $path
     * @throws \Throwable
     */
    public function __construct(string $path) {
        if(!file_exists($path)) {
            throw new \RuntimeException(sprintf("Log file not found at: %s", $path));
        }

        try {
            $stream = fopen($path, 'r+');
            $this->stream = $stream;
        } catch (\Throwable $exception) {
            // @TODO: Handle the exception
            // For now throw it again
            throw $exception;
        }
    }

    /**
     * @param int $length
     * @return bool|string
     */
    public function read(int $length = 4096) {
        return fread($this->stream, $length);
    }

    /**
     * @return array
     */
    public function getFileDetails(): array {
        return fstat($this->stream);
    }
}