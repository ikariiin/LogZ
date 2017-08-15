<?php
/**
 * Created by PhpStorm.
 * User: saitama
 * Date: 8/11/17
 * Time: 9:44 PM
 */

namespace LogParser;


class LogMessage {
    /**
     * @var string
     */
    private $message;

    /**
     * @var int
     */
    private $lineNumber;

    /**
     * @var string
     */
    private $fileName;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * LogMessage constructor.
     * @param string $message
     */
    public function __construct(string $message) {
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function parse(): bool {
        $this
            ->_getLineNumber()
            ->_getFileName();
        return true;
    }

    private function _getLineNumber(): self {
        $expOp = explode("on line", $this->message);
        $lineNumber = null;

        if(count($expOp) === 2) {
            $lineNumber = $expOp[count($expOp) - 1];
        } elseif (count($expOp) === 1) {
            $expOp = explode(":", $this->message);
            $lineNumber = $expOp[count($expOp) - 1];
        }

        $this->lineNumber = (int) $lineNumber;

        return $this;
    }

    private function _getFileName(): self {
        $expOp = explode('in /', $this->message);
        $this->_getErrorMessage($expOp);
        $fileRedundant = $expOp[count($expOp) - 1];
        $fileName = explode("on line", $fileRedundant);
        if(count($fileName) === 2) {
            $fileName = $fileName[0];
        } else {
            $fileName = explode(':', $fileRedundant)[0];
        }
        $fileName = trim('/' . $fileName);
        $this->fileName = $fileName;
        return $this;
    }

    private function _getErrorMessage(array $expOp): self {
        for ($i = 0; $i < count($expOp) - 1; $i++) {
            $this->errorMessage .= $expOp[$i];
        }
        $this->errorMessage = trim($this->errorMessage);

        return $this;
    }

    /**
     * @return string
     */
    public function getFileName(): string {
        return $this->fileName;
    }

    /**
     * @return int
     */
    public function getLineNumber(): int {
        return $this->lineNumber;
    }

    public function getErrorMessage(): string {
        return $this->errorMessage;
    }
}