<?php
namespace LogParser;

class Log {
    const SEVERITY_TYPES = [
        "WARNING" => 1,
        "NOTICE" => 2,
        "PARSE-ERROR" => 3,
        "FATAL-ERROR" => 4,
        "EXCEPTION" => 5
    ];
    /**
     * @var int
     */
    private $severity;

    /**
     * @var \DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $content;

    /**
     * @var LogMessage
     */
    private $message;

    /**
     * @param int $severity
     * @return Log
     */
    public function setSeverity(int $severity): self {
        $this->severity = $severity;
        return $this;
    }

    /**
     * @return int
     */
    public function getSeverity(): int {
        return $this->severity;
    }

    /**
     * @param \DateTimeImmutable $date
     * @return Log
     */
    public function setDate(\DateTimeImmutable $date): self {
        $this->date = $date;
        return $this;
    }

    /**
     * @param string $content
     * @return Log
     */
    public function setContent(string $content): self {
        $this->setMessage($content);
        $this->content = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent(): string {
        return $this->content;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getDate(): \DateTimeImmutable {
        return $this->date;
    }

    /**
     * @param string $content
     * @return Log
     */
    public function setMessage(string $content): self {
        $this->message = new LogMessage($content);
        return $this;
    }

    /**
     * @return LogMessage
     */
    public function getMessage(): LogMessage {
        $this->message->parse();
        return $this->message;
    }
}