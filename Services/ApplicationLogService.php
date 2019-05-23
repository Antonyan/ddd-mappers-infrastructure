<?php

namespace Infrastructure\Services;

use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Models\Logging\CloudWatchProvider;
use Infrastructure\Models\Logging\Logger;

class ApplicationLogService extends LogService
{
    protected function getChannelName(): string
    {
        return 'application';
    }
}