<?php

namespace Infrastructure\Models\Http;


use Psr\Http\Message\StreamInterface;

class Request
{
    /**
     * @var \GuzzleHttp\Psr7\Request
     */
    private $guzzleRequest;

    /**
     * Request constructor.
     * @param string $method
     * @param $uri
     * @param array $headers
     * @param null $body
     * @param string $version
     */
    public function __construct(string $method, $uri, array $headers, $body = null, $version = '1.1')
    {
        $this->guzzleRequest = new \GuzzleHttp\Psr7\Request(
            $method,
            $uri,
            $headers,
            $body,
            $version
        );
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->guzzleRequest->getMethod();
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->guzzleRequest->getUri();
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->guzzleRequest->getHeaders();
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): StreamInterface
    {
        $this->guzzleRequest->getBody();
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->guzzleRequest->getProtocolVersion();
    }
}