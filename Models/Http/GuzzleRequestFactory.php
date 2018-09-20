<?php

namespace Infrastructure\Models\Http;


use Psr\Http\Message\RequestInterface;

class GuzzleRequestFactory extends AbstractRequestFactory
{
    /**
     * @param $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @return RequestInterface
     */
    public function create($method, $uri, array $headers = [], $body = null): RequestInterface
    {
        return new \GuzzleHttp\Psr7\Request($method, $uri, $headers, $body);
    }
}