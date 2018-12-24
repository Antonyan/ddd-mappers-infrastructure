<?php

namespace Infrastructure\Services;

use Infrastructure\Models\Logging\CloudWatchLoggingRegistry;
use Infrastructure\Models\Logging\FileLoggingRegistry;

class ApplicationLogService extends LogService
{
    /**
     * @return array
     */
    protected function loggersMap(): array
    {
        return [
            self::LOG_TO_FILE => function () {return (new FileLoggingRegistry())->logger(LOG_PATH . 'application.log', 'application');},
            self::LOG_TO_CLOUD_WATCH => function () {return (new CloudWatchLoggingRegistry())->logger('php-logs', 'application', 'core');},
        ];
    }
}