<?php
namespace Infrastructure\Factories;

use Exception;
use Psr\Log\LoggerInterface;

interface LogFactory
{
    /**
     * @param string $channel
     * @return LoggerInterface
     * @throws Exception
     */
    public function create(string $channel) : LoggerInterface;
}