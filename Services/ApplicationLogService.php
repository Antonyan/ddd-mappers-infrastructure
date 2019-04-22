<?php

namespace Infrastructure\Services;

use Infrastructure\Factories\LoggerFactory;

class ApplicationLogService extends LogService
{
    /**
     * @var string
     */
    private $loggerFactory;

    /**
     * ApplicationLogService constructor.
     * @param LoggerFactory $loggerFactory
     */
    public function __construct(LoggerFactory $loggerFactory)
    {
        $this->loggerFactory = $loggerFactory;
    }

    /**
     * @return array
     * @throws \Infrastructure\Exceptions\InfrastructureException
     */
    protected function loggersMap(): array
    {
        return [
            self::LOG_TO_FILE => $this->loggerFactory->create(LoggerFactory::FILE, 'application'),
            self::LOG_TO_CLOUD_WATCH => $this->loggerFactory->create(LoggerFactory::CLOUD_WATCH, 'application'),
        ];
    }
}