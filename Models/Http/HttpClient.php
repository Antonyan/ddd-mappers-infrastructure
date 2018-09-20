<?php

namespace Infrastructure\Models\Http;


use GuzzleHttp\Client;
use Infrastructure\Models\Http\Response\ResponseFactory;
use Psr\Http\Message\RequestInterface;

class HttpClient
{
    /**
     * @var Client
     */
    private $guzzleHttpClient;

    /**
     * @var ResponseFactory
     */
    private $responseFactory;

    public function __construct(ResponseFactory $responseFactory = null)
    {
        $this->guzzleHttpClient = new Client();
        $this->responseFactory = $responseFactory ?? new ResponseFactory();
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws Response\ResponseContentTypeException
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        return $this->responseFactory->createFromResponse($this->guzzleHttpClient->send($request));
    }
}