<?php

namespace Infrastructure\Services;

use Exception;
use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Models\Config;
use Infrastructure\Models\ServiceContainerBuilderFactory;
use ReflectionException;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\BadMethodCallException;

abstract class BaseService
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @return ContainerBuilder
     * @throws ReflectionException
     * @throws InfrastructureException
     * @throws Exception
     */
    protected function container() : ContainerBuilder
    {
        if ($this->container === null) {
            $this->container = (new ServiceContainerBuilderFactory())->create($this);
        }

        return $this->container;
    }

    /**
     * @return Config
     * @throws ReflectionException
     * @throws Exception
     */
    protected function config() : Config
    {
        return $this->container()->get('config');
    }
}