<?php
namespace LogParser;

class Parser {
    /**
     * @var ErrorLogFile
     */
    private $file;

    /**
     * @var string|array
     */
    private $intermediateStore;

    /**
     * @var array
     */
    private $logs = [];

    public function __construct(ErrorLogFile $file) {
        $this->file = $file;
    }

    public function start(): void {
        $this->intermediateStore = $this
            ->file
            ->load()
            ->read(
                $this
                    ->file
                    ->load()
                    ->getFileDetails()["size"]
            );

        $this
            ->breakNewLines()
            ->categorize()
            ->parseDate()
            ->parseMessage()
            ->stop();
    }

    private function breakNewLines(): self {
        $this->intermediateStore = explode(PHP_EOL, $this->intermediateStore);
        return $this;
    }

    private function categorize(): self {
        foreach ($this->intermediateStore as $k => &$errorLog) {
            $log = new Log();

            if(strlen($errorLog) === 0) {
                continue;
            } elseif (strpos($errorLog, "Stack trace:") === 0 || strpos($errorLog, "#") === 0 || strpos($errorLog, "thrown") === 2) {
                // We will handle stack traces later. For now, continue.
                continue;
            } elseif (strpos($errorLog, "PHP Warning") === 27 || strpos($errorLog, "PHP Warning") === 28) {
                $log->setSeverity(Log::SEVERITY_TYPES["WARNING"]);
                $errorLog = trim(str_replace("PHP Warning:", "", $errorLog));
            } elseif (strpos($errorLog, "PHP Notice") === 27 || strpos($errorLog, "PHP Notice") == 28) {
                $log->setSeverity(Log::SEVERITY_TYPES["NOTICE"]);
                $errorLog = trim(str_replace("PHP Notice:", "", $errorLog));
            } elseif (strpos($errorLog, "PHP Parse error") === 27 || strpos($errorLog, "PHP Parse Error") === 28) {
                $log->setSeverity(Log::SEVERITY_TYPES["PARSE-ERROR"]);
                $errorLog = trim(str_replace("PHP Parse error:", "", $errorLog));
            } elseif (strpos($errorLog, "PHP Fatal error") === 27 || strpos($errorLog, "PHP Fatal Error") === 28) {
                $log->setSeverity(Log::SEVERITY_TYPES["FATAL-ERROR"]);
                $errorLog = trim(str_replace("PHP Fatal error:", "", $errorLog));
            } elseif (strpos($errorLog, "PHP Exception") === 27 || strpos($errorLog, "PHP Exception") === 28) {
                $log->setSeverity(Log::SEVERITY_TYPES["EXCEPTION"]);
                $errorLog = trim(str_replace("PHP Exception:", "", $errorLog));
            }

            $this->logs[$k] = $log;
        }
        return $this;
    }

    private function parseDate(): self {
        foreach ($this->intermediateStore as $k => &$errorLog) {
            if(strlen($errorLog) === 0) {
                continue;
            } elseif (strpos($errorLog, "Stack trace:") === 0 || strpos($errorLog, "#") === 0 || strpos($errorLog, "thrown") === 2) {
                // We will handle stack traces later. For now, continue.
                continue;
            }
            $matches = [];
            $matchOp = preg_match("/\[(.*?)\]/s", $errorLog, $matches);
            if($matchOp) {
                $dateStr = $matches[1];
                $errorLog = trim(str_replace($matches[0], '', $errorLog));
                $dateStrTZExp = explode(' ', $dateStr);
                $dateTime = \DateTimeImmutable::createFromFormat(
                    "j-M-Y H:i:s",
                    $dateStrTZExp[0] . ' ' . $dateStrTZExp[1],
                    new \DateTimeZone($dateStrTZExp[2])
                );

                ($this->logs[$k])->setDate($dateTime);
            } else {
                continue;
            }
        }
        return $this;
    }

    private function parseMessage(): self {
        foreach ($this->intermediateStore as $k => &$errorLog) {
            if(strlen($errorLog) === 0) {
                continue;
            } elseif (strpos($errorLog, "Stack trace:") === 0 || strpos($errorLog, "#") === 0 || strpos($errorLog, "thrown") === 2) {
                // We will handle stack traces later. For now, continue.
                continue;
            }

            $this->logs[$k]->setContent($errorLog);
        }
        return $this;
    }

    private function stop(): self {
        return $this;
    }

    private function resetArray(array $logs): array {
        $arr = [];
        foreach ($logs as $log) {
            $arr[] = $log;
        }
        return $arr;
    }

    /**
     * @return array
     */
    public function getLogs(): array {
        return $this->resetArray($this->logs);
    }
}