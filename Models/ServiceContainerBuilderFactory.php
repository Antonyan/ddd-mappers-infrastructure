<?php
namespace Infrastructure\Models;

use Infrastructure\Services\BaseService;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ServiceContainerBuilderFactory
{
    /**
     * @param $service
     * @return ContainerBuilder
     */
    public function create($service)
    {
        $dir = \dirname((new \ReflectionClass($service))->getFileName());

        $infrastructureDir = \dirname((new \ReflectionClass(BaseService::class))->getFileName());

        $containerBuilder = new ContainerBuilder();

        $containerBuilder
            ->register('config', Config::class)->setArgument('$config', include $dir . '/../config/config.php');

        $containerBuilder->get('config')->merge(new Config(include $infrastructureDir . '/../config/config.php'));

        include $dir . '/../config/container.php';

        include $infrastructureDir . '/../config/container.php';

        return $containerBuilder;
    }
}