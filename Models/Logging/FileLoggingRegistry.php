<?php

namespace Infrastructure\Models\Logging;

use Exception;
use Psr\Log\LoggerInterface;

class FileLoggingRegistry
{
    /**
     * @var array
     */
    private $logStructure;

    /**
     * @param $fileName
     * @param $channel
     * @return LoggerInterface
     * @throws Exception
     */
    public function logger($fileName, $channel) : LoggerInterface
    {
        if (isset ($this->logStructure[$fileName])){
            return $this->logStructure[$fileName];
        }

        $this->logStructure[$fileName] = new Logger($channel, [(new Stream())->handler($fileName)]);

        return $this->logStructure[$fileName];
    }
}