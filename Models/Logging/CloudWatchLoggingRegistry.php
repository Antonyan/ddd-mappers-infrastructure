<?php

namespace Infrastructure\Models\Logging;

use Infrastructure\Exceptions\InfrastructureException;
use Psr\Log\LoggerInterface;

class CloudWatchLoggingRegistry
{
    /**
     * @var array
     */
    private $logStructure;

    /**
     * @param $groupName
     * @param $streamName
     * @param $channel
     * @return Logger
     * @throws InfrastructureException
     */
    public function logger($groupName, $streamName, $channel) : LoggerInterface
    {
        if (isset ($this->logStructure[$groupName][$streamName][$channel])){
            return $this->logStructure[$groupName][$streamName][$channel];
        }

        $this->logStructure[$groupName][$streamName][$channel] = new Logger($channel, [(new CloudWatch())->handler($groupName, $streamName)]);

        return $this->logStructure[$groupName][$streamName][$channel];
    }
}