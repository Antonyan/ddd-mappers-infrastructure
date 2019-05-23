<?php
namespace Infrastructure\Factories;

use Psr\Log\LoggerInterface;

interface LogFactory
{
    /**
     * @param string $channel
     * @return LoggerInterface
     */
    public function create(string $channel) : LoggerInterface;
}