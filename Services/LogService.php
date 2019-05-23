<?php

namespace Infrastructure\Services;

use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Factories\LoggerFactory;
use Infrastructure\Models\Logging\LoggerNullObject;
use Psr\Log\LoggerInterface;
use ReflectionException;

abstract class LogService extends BaseService
{
    /**
     * @return LoggerInterface
     * @throws InfrastructureException
     * @throws ReflectionException
     */
    public function getLogger() : LoggerInterface
    {
        if (!$this->config()->loggingType()){
            return new LoggerNullObject();
        }

        if (!array_key_exists($this->config()->loggingType(), $this->loggersMap())){
            throw new InfrastructureException('Unknown logging type ' . $this->config()->loggingType());
        }

        return $this->getLoggerFactory()->create($this->config()->loggingType(), $this->getChannelName());
    }

    abstract protected function getChannelName() : string;

    /**
     * @return LoggerFactory
     * @throws InfrastructureException
     * @throws ReflectionException
     */
    protected function getLoggerFactory() : LoggerFactory
    {
        return $this->container()->get('LoggerFactory');
    }
}