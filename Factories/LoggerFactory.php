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
     * @var FileLogFactory
     */
    private $fileLogFactory;

    /**
     * @var CloudWatchLogFactory
     */
    private $cloudWatchLogFactory;

    private $loggers = [];

    /**
     * LoggerFactory constructor.
     * @param FileLogFactory $fileLogFactory
     * @param CloudWatchLogFactory $cloudWatchLogFactory
     */
    public function __construct(
        FileLogFactory $fileLogFactory,
        CloudWatchLogFactory $cloudWatchLogFactory
    ) {
        $this->fileLogFactory = $fileLogFactory;
        $this->cloudWatchLogFactory = $cloudWatchLogFactory;
    }

    /**
     * @param string $type
     * @param string $channelName
     * @return LoggerInterface
     * @throws InfrastructureException
     */
    public function create(string $type, string $channelName): LoggerInterface
    {
        if (!isset($this->getLoggersMap()[$type])) {
            throw new InfrastructureException('A logger for the needed type( "' . $type . '" ) is not found');
        }

        if (isset($this->loggers[$type][$channelName])) {
            return $this->loggers[$type][$channelName];
        }

        return $this->getLoggersMap()[$type]->create($channelName);

    }

    /**
     * @return LogFactory[]
     */
    protected function getLoggersMap(): array
    {
        return [
            self::FILE => $this->fileLogFactory,
            self::CLOUD_WATCH => $this->cloudWatchLogFactory,
        ];
    }
}
