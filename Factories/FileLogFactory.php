<?php

namespace Infrastructure\Factories;

use Exception;
use Infrastructure\Models\Logging\Logger;
use Infrastructure\Models\Logging\StreamProvider;
use Psr\Log\LoggerInterface;

class FileLogFactory implements LogFactory
{
    private $logPath;

    private $provider;

    /**
     * FileLogFactory constructor.
     * @param string $logPath
     * @param StreamProvider $provider
     */
    public function __construct(string $logPath, StreamProvider $provider)
    {
        $this->logPath = $logPath;
        $this->provider = $provider;
    }

    /**
     * @param string $channel
     * @return LoggerInterface
     * @throws Exception
     */
    public function create(string $channel) : LoggerInterface
    {
        return new Logger($channel, [$this->provider->handler($this->buildFileName($channel))]);
    }

    private function buildFileName(string $channel)
    {
        return $this->logPath . $channel . 'Log.log';
    }
}