<?php

namespace Infrastructure\Models\Http;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Infrastructure\Exceptions\InternalException;
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
     * @throws InternalException
     * @throws Response\ResponseContentTypeException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->responseFactory->createFromResponse($this->guzzleHttpClient->send($request));
        } catch (RequestException $exception) {
            $response = $exception->getResponse();

            throw new InternalException(
                $response->getBody()->getContents(),
                $response->getStatusCode(),
                $response->getHeaders(),
                $exception,
                $exception->getCode()
            );
        }
    }
}