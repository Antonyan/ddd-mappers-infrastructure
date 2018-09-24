<?php

namespace Infrastructure\Models\Http;


use Psr\Http\Message\RequestInterface;

interface RequestFactoryInterface
{
    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @return RequestInterface
     */
    public function create($method, $uri, array $headers = [], $body = null): RequestInterface;
}