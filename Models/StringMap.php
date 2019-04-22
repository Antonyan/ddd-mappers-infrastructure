<?php

namespace Infrastructure\Models;

class StringMap implements \Countable
{
    /**
     * @var array
     */
    private $collection = [];

    /**
     * @param string $key
     * @param string $default
     * @return string
     */
    public function get(string $key, string $default = ''): string
    {
        if (!$this->has($key)) {
            return $default;
        }

        return $this->collection[$key];
    }

    /**
     * @param string $key
     * @param string $value
     * @return StringMap
     */
    public function add(string $key, string $value): StringMap
    {
        $this->collection[$key] = $value;
        return $this;
    }

    /**
     * @param array $data An associative array in format [string => string]
     * @return StringMap
     */
    public function addAll(array $data): StringMap
    {
        array_walk($data, 'add');
        return $this;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function remove(string $key): bool
    {
        if (!$this->has($key)) {
            return false;
        }

        unset($this->collection[$key]);
        return true;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return array_key_exists($key, $this->collection);
    }

    /**
     * @param string $key
     * @param string $value
     * @param string $glue
     * @return StringMap
     */
    public function mergeIfExist(string $key, string $value, string $glue = ';'): StringMap
    {
        if (!$this->has($key)) {
            return $this->add($key, $value);
        }

        $this->add($key, $this->get($key) . $glue . $value);

        return $this;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->collection;
    }

    /**
     * Count elements of an object
     * @return int The custom count as an integer.
     */
    public function count()
    {
        return count($this->collection);
    }
}