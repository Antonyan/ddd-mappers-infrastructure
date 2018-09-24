<?php

namespace Infrastructure\Tests;



trait ReflectionHelperTrait
{
    /**
     * @param $object
     * @param string $method
     * @param mixed ...$arguments
     * @return mixed
     * @throws \ReflectionException
     */
    private function callMethod($object, string $method, ...$arguments)
    {
        $reflector = new \ReflectionClass($object);

        $method = $reflector->getMethod($method);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $arguments);
    }

    /**
     * @param $object
     * @param string $property
     * @return mixed
     * @throws \ReflectionException
     */
    private function getProperty($object, string $property)
    {
        $reflector = new \ReflectionClass($object);

        $property = $reflector->getProperty($property);
        $property->setAccessible(true);

        return $property->getValue($object);
    }

    /**
     * @param $object
     * @param string $property
     * @param $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setPrivatePropertyInMock($object, string $property, $value)
    {
        $reflector = new \ReflectionClass(get_parent_class($object));

        $property = $reflector->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);

        return $object;
    }

    /**
     * @param $object
     * @param string $property
     * @param $value
     * @return mixed
     * @throws \ReflectionException
     */
    private function setProperty($object, string $property, $value)
    {
        $reflector = new \ReflectionClass($object);

        $property = $reflector->getProperty($property);
        $property->setAccessible(true);
        $property->setValue($object, $value);

        return $object;
    }
}