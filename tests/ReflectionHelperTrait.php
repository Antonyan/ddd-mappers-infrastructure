<?php

namespace Infrastructure\Tests;


use ReflectionObject;

trait ReflectionHelperTrait
{
    /**
     * @param $object
     * @param string $method
     * @param mixed ...$arguments
     * @return mixed
     */
    private function callMethod($object, string $method, ...$arguments)
    {
        $reflector = new ReflectionObject($object);

        $method = $reflector->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $arguments);
    }
}