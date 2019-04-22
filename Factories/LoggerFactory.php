<?php

namespace Infrastructure\Factories;


use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Models\Logging\CloudWatchLoggingRegistry;
use Infrastructure\Models\Logging\FileLoggingRegistry;

class LoggerFactory
{
    public const FILE = 'file';
    public const CLOUD_WATCH = 'cloudWatch';

    public const ALLOWED_LOGGER_TYPES = [
        self::FILE,
        self::CLOUD_WATCH,
    ];

    /**
     * @var FileLoggingRegistry
     */
    private $fileLoggingRegistry;

    /**
     * @var CloudWatchLoggingRegistry
     */
    private $cloudWatchLoggingRegistry;

    /**
     * @var string
     */
    private $logPath;

    /**
     * @var string
     */
    private $applicationName;

    /**
     * LoggerFactory constructor.
     * @param string $logPath
     * @param string $applicationName
     */
    public function __construct(string $logPath, string $applicationName = '')
    {
        $this->fileLoggingRegistry = new FileLoggingRegistry();
        $this->cloudWatchLoggingRegistry = new CloudWatchLoggingRegistry();
        $this->logPath = $logPath;
        $this->applicationName = $applicationName;
    }

    /**
     * @param string $type
     * @param string $channelName
     * @return callable
     * @throws InfrastructureException
     */
    public function create(string $type, string $channelName): callable
    {
        if (!in_array($type, static::ALLOWED_LOGGER_TYPES, true)) {
            throw new InfrastructureException('A logger for the needed type( "' . $type . '" ) is not found');
        }

        return function() use ($type, $channelName) { return $this->getLoggersMap()[$type]($channelName);};
    }

    /**
     * @return array
     */
    protected function getLoggersMap(): array
    {
        return [
            self::FILE => function(string $channelName) {
                return $this->fileLoggingRegistry->logger($this->logPath . $channelName . 'Log.log', $channelName);
            },
            self::CLOUD_WATCH => function(string $channelName) {
                return $this->cloudWatchLoggingRegistry->logger($this->getGroupName(), $this->getStreamName(), $channelName);
            },
        ];
    }

    /**
     * @return string
     */
    protected function getStreamName(): string
    {
        return getenv('ENV') . '-' . $this->applicationName;
    }

    /**
     * @return string
     */
    protected function getGroupName(): string
    {
        return 'php-log';
    }
}
