<?php

namespace Infrastructure\Models\Http;


use Infrastructure\Models\ArraySerializable;

class Headers implements ArraySerializable
{
    /**
     * @var array
     */
    private $headersContainer = [];

    /**
     * HeadersBuilder constructor.
     * @param array $headers
     * @throws IllegalHeaderValueException
     */
    public function __construct(array $headers = [])
    {
          foreach ($headers as $name => $value) {
              $this->addHeader($name, $value);
          }
    }

    /**
     * @param Headers $headers
     * @return Headers
     * @throws IllegalHeaderValueException
     */
    public function merge(Headers $headers): Headers
    {
        return new Headers($headers->toArray() + $this->toArray());
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->headersContainer;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return Headers
     *
     * @throws IllegalHeaderValueException
     */
    protected function addHeader(string $name, $value): Headers
    {
        if (!is_scalar($value) && !is_array($value)) {
            throw new IllegalHeaderValueException($value);
        }

        if (is_scalar($value)) {
            $this->addHeaderValue($name, $value);

            return $this;
        }

        foreach ($value as $item) {
            $this->addHeaderValue($name, $item);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return Headers
     *
     * @throws IllegalHeaderValueException
     */
    protected function addHeaderValue(string $name, $value)
    {
        if (!is_scalar($value)) {
            throw new IllegalHeaderValueException($value);
        }

        if (!array_key_exists($name, $this->headersContainer)) {
            $this->headersContainer[$name] = [];
        }

        $this->headersContainer[$name][] = $value;

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasHeader(string $name): bool
    {
        return array_key_exists($name, $this->headersContainer);
    }
}