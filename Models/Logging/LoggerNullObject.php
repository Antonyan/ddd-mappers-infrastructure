<?php

namespace Infrastructure\Models\Logging;

use Psr\Log\LoggerInterface;

class LoggerNullObject implements LoggerInterface
{
    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array()) : void {}

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array()) : void {}

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array()) : void {}
}