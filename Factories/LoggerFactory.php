<?php

namespace Infrastructure\Factories;

use Infrastructure\Exceptions\InfrastructureException;
use Psr\Log\LoggerInterface;

class LoggerFactory
{
    public const FILE = 'file';
    public const CLOUD_WATCH = 'cloudWatch';
    public const SYSLOG = 'syslog';
    public const ERROR_LOG = 'errorLog';

    /**
     * @var LogFactory[]
     */
    private $logFactoryMap;

    /**
     * @var array
     */
    private $loggers = [];

    /**
     * LoggerFactory constructor.
     * @param array $logFactoryMap
     */
    public function __construct(array $logFactoryMap) {
        $this->logFactoryMap = $logFactoryMap;
    }

    /**
     * @param string $type
     * @param string $channelName
     * @return LoggerInterface
     * @throws InfrastructureException
     */
    public function create(string $type, string $channelName): LoggerInterface
    {
        if (!isset($this->logFactoryMap[$type])) {
            throw new InfrastructureException('A logger for the needed type( "' . $type . '" ) is not found');
        }

        if (isset($this->loggers[$type][$channelName])) {
            return $this->loggers[$type][$channelName];
        }

        return $this->logFactoryMap[$type]->create($channelName);
    }
}
