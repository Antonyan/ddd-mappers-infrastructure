<?php

namespace Infrastructure\Factories;

use Exception;
use Infrastructure\Models\Logging\Logger;
use Infrastructure\Models\Logging\Stream;
use Psr\Log\LoggerInterface;

class FileLogFactory implements LogFactory
{
    private $logPath;

    /**
     * FileLogFactory constructor.
     * @param string $logPath
     */
    public function __construct(string $logPath)
    {
        $this->logPath = $logPath;
    }

    /**
     * @param string $channel
     * @return LoggerInterface
     * @throws Exception
     */
    public function create(string $channel) : LoggerInterface
    {
        return new Logger($channel, [(new Stream())->handler($this->buildFileName($channel))]);
    }

    private function buildFileName(string $channel)
    {
        return $this->logPath . $channel . 'Log.log';
    }
}