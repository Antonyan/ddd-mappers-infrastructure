<?php

namespace Infrastructure\Models\Http;


class HeadersBuilder
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
     * @return array
     */
    public function build(): array
    {
        return $this->headersContainer;
    }

    /**
     * @param string $name
     * @param $value
     *
     * @return HeadersBuilder
     *
     * @throws IllegalHeaderValueException
     */
    public function addHeader(string $name, $value): HeadersBuilder
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
     * @return HeadersBuilder
     *
     * @throws IllegalHeaderValueException
     */
    public function addHeaderValue(string $name, $value)
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