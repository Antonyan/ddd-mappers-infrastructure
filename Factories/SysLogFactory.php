<?php

namespace Infrastructure\Factories;

use Exception;
use Infrastructure\Models\Logging\Logger;
use Infrastructure\Models\Logging\SysLogProvider;
use Psr\Log\LoggerInterface;

class SysLogFactory implements LogFactory
{
    private $provider;

    /**
     * ErrorLogFactory constructor.
     * @param SysLogProvider $provider
     */
    public function __construct(SysLogProvider $provider)
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