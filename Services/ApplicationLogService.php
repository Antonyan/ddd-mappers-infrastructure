<?php

namespace Infrastructure\Services;

use Infrastructure\Models\Logging\CloudWatchLoggingRegistry;
use Infrastructure\Models\Logging\FileLoggingRegistry;

class ApplicationLogService extends LogService
{
    /**
     * @var string
     */
    private $streamName;

    /**
     * ApplicationLogService constructor.
     * @param string $applicationName
     * @param string $environment
     */
    public function __construct(string $applicationName, string $environment)
    {
        $this->streamName = $applicationName . '-' . $environment . ' -application';
    }

    /**
     * @return array
     */
    protected function loggersMap(): array
    {
        return [
            self::LOG_TO_FILE => function () {return (new FileLoggingRegistry())->logger(LOG_PATH . $this->streamName . '.log', 'application');},
            self::LOG_TO_CLOUD_WATCH => function () {return (new CloudWatchLoggingRegistry())->logger('php-logs', $this->streamName, 'core');},
        ];
    }
}