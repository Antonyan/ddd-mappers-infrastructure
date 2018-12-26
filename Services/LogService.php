<?php

namespace Infrastructure\Services;

use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Models\Logging\LoggerNullObject;
use Psr\Log\LoggerInterface;
use ReflectionException;

abstract class LogService extends BaseService
{
    protected const LOG_TO_FILE = 'file';
    protected const LOG_TO_CLOUD_WATCH = 'cloudWatch';

    /**
     * @return LoggerInterface
     * @throws InfrastructureException
     * @throws ReflectionException
     */
    public function getLogger()
    {
        if (!$this->config()->loggingType()){
            return new LoggerNullObject();
        }

        if (!array_key_exists($this->config()->loggingType(), $this->loggersMap())){
            throw new InfrastructureException('Unknown logging type ' . $this->config()->loggingType());
        }

        return $this->loggersMap()[$this->config()->loggingType()]();
    }

    abstract protected function loggersMap() : array;
}