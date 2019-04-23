<?php

namespace Infrastructure\Services;

use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Models\Logging\CloudWatch;
use Infrastructure\Models\Logging\Logger;

class ApplicationLogService extends LogService
{
    /**
     * @var string
     */
    private $loggerFactory;

    /**
     * @var string
     */
    private $applicationName;

    /**
     * ApplicationLogService constructor.
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(LoggerFactory $loggerFactory, string $applicationName)
    {
        $this->loggerFactory = $loggerFactory;
        $this->applicationName = $applicationName;
    }

    /**
     * @return array
     * @throws \Infrastructure\Exceptions\InfrastructureException
     */
    protected function loggersMap(): array
    {
        return [
            self::LOG_TO_FILE => $this->loggerFactory->create(LoggerFactory::FILE, 'application'),
            self::LOG_TO_CLOUD_WATCH => new Logger('application', [(new CloudWatch())->handler($this->loggerFactory->getGroupName(), $this->loggerFactory->getStreamName() . '-application')])
        ];
    }
}