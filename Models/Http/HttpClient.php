<?php

namespace Infrastructure\Models\Http;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Infrastructure\Exceptions\HttpExceptionInterface;
use Infrastructure\Exceptions\InfrastructureException;
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
                HttpExceptionInterface::DEFAULT_ERROR_CODE,
                $response->getHeaders(),
                [],
                $exception,
                $exception->getCode()
            );
        } catch (GuzzleException $exception) {
            throw new InfrastructureException('Guzzle Exception', $exception);
        }
    }
}