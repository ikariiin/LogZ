<?php
/**
 * Created by PhpStorm.
 * User: saitama
 * Date: 8/12/17
 * Time: 5:12 PM
 */

namespace LogParser;


class JSONify {
    /**
     * @var array
     */
    private $logs;

    /**
     * @var array
     */
    private $equivLogs = [];

    /**
     * JSONify constructor.
     * @param array $logs
     */
    public function __construct(array $logs) {
        $this->logs = $logs;
    }

    /**
     * @return JSONify
     */
    public function convert(): self {
        foreach ($this->logs as $log) {
            /** @var $log Log */
            $equivLog = [
                "date" => $log->getDate()->format("Y-m-d H:i:s"),
                "severity" => $log->getSeverity(),
                "originalMessage" => $log->getContent()
            ];
            $messageEquiv = $log->getMessage();
            $equivLog["message"] = [
                "fileName" => $messageEquiv->getFileName(),
                "lineNumber" => $messageEquiv->getLineNumber(),
                "error" => $messageEquiv->getErrorMessage()
            ];
            $this->equivLogs[] = $equivLog;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function getJSON(): string {
        return json_encode($this->equivLogs, JSON_PRETTY_PRINT);
    }
}