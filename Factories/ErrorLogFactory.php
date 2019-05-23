<?php

namespace Infrastructure\Factories;

use Exception;
use Infrastructure\Models\Logging\ErrorLogProvider;
use Infrastructure\Models\Logging\Logger;
use Psr\Log\LoggerInterface;

class ErrorLogFactory implements LogFactory
{
    private $provider;

    /**
     * ErrorLogFactory constructor.
     * @param ErrorLogProvider $provider
     */
    public function __construct(ErrorLogProvider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * @param string $channel
     * @return LoggerInterface
     * @throws Exception
     */
    public function create(string $channel) : LoggerInterface
    {
        return new Logger($channel, [$this->provider->handler()]);
    }
}