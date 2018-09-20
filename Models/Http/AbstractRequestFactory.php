<?php

namespace Infrastructure\Models\Http;


use Psr\Http\Message\RequestInterface;

abstract class AbstractRequestFactory
{
    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @return RequestInterface
     */
    abstract public function create($method, $uri, array $headers = [], $body = null): RequestInterface;
}