<?php
namespace Infrastructure\Services;

use Infrastructure\Models\Config;
use Infrastructure\Models\ServiceContainerBuilderFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\ContainerBuilder;

abstract class BaseCommand extends Command
{
    /**
     * @var ContainerBuilder
     */
    private $container;

    /**
     * @return ContainerBuilder
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
     */
    protected function config() : Config
    {
        return $this->container()->get('config');
    }
}