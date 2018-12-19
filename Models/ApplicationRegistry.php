<?php
namespace Infrastructure\Services;

use Infrastructure\Exceptions\ApplicationRegistryException;

class ApplicationRegistry
{
    private static $instance;

    private $storage = [];

    private function __construct()
    {
    }

    private function __clone()
    {
    }

    private function __wakeup()
    {
    }

    public static function getInstance(): ApplicationRegistry
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function set($key, $value)
    {
        $this->storage[$key] = $value;
    }

    public function get($key)
    {
        if (! array_key_exists($key, $this->storage)) {
            throw new ApplicationRegistryException("Item with $key not found in registry!");
        }

        return $this->storage[$key];
    }
}