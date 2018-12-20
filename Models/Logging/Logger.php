<?php

namespace Infrastructure\Models\Logging;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger as MonologLogger;
use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{
    /**
     * @var $channelName
     */
    private $channelName;

    /**
     * @var AbstractProcessingHandler[]
     */
    private $handlers;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Logger constructor.
     * @param $channelName
     * @param AbstractProcessingHandler[] $handlers
     */
    public function __construct($channelName, array $handlers)
    {
        $this->channelName = $channelName;
        $this->handlers = $handlers;
    }

    /**
     * @return LoggerInterface
     */
    private function logger()
    {
        if ($this->logger !== null) {
            return $this->logger;
        }

        $this->logger = (new MonologLogger($this->channelName))->setHandlers($this->handlers);

        return $this->logger;
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array()) : void
    {
        $this->logger()->emergency($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array()) : void
    {
        $this->logger()->alert($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array()) : void
    {
        $this->logger()->critical($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array()) : void
    {
        $this->logger()->error($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array()) : void
    {
        $this->logger()->warning($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array()) : void
    {
        $this->logger()->notice($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array()) : void
    {
        $this->logger()->info($message, $context);
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array()) : void
    {
        $this->logger()->debug($message, $context);
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array()) : void
    {
        $this->logger()->log($level, $message, $context);
    }
}