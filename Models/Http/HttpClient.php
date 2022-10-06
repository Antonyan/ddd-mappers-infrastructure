<?php
namespace Infrastructure\Models\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Infrastructure\Exceptions\HttpExceptionInterface;
use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Exceptions\InternalException;
use Infrastructure\Models\ErrorData;
use Infrastructure\Models\Http\Response\ResponseFactory;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as PsrResponseInterface;

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
     */
    public function send(RequestInterface $request): ResponseInterface
    {
        try {
            return $this->responseFactory->createFromResponse($this->guzzleHttpClient->send($request));
        } catch (RequestException $exception) {
            $response = $exception->getResponse();

            throw new InternalException(
                $exception->getMessage(),
                HttpExceptionInterface::DEFAULT_ERROR_CODE,
                (new ErrorData())->add('content', $response->getBody()->getContents()),
                $response->getStatusCode(),
                (new ErrorData())->addAll($this->getResponseHeadersFormatted($request->getHeaders())),
                $exception,
                $exception->getCode()
            );
        } catch (GuzzleException $exception) {
            throw new InfrastructureException('Guzzle Exception', $exception);
        }
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return ResponseInterface
     * @throws InfrastructureException
     * @throws InternalException
     */
    public function request(string $method, string $uri = '', array $options = []): ResponseInterface
    {
        try {
            return $this->responseFactory->createFromResponse($this->guzzleHttpClient->request($method, $uri, $options));
        } catch (RequestException $exception) {
            $response = $exception->getResponse();
            throw new InternalException(
                $exception->getMessage(),
                HttpExceptionInterface::DEFAULT_ERROR_CODE,
                (new ErrorData())->add('content', $response->getBody()->getContents()),
                $response->getStatusCode(),
                (new ErrorData())->addAll($this->getResponseHeadersFormatted($response->getHeaders())),
                $exception,
                $exception->getCode()
            );
        } catch (GuzzleException $exception) {
            throw new InfrastructureException('Guzzle Exception', $exception);
        }
    }

    /**
     * @param array $headers
     * @return array
     */
    private function getResponseHeadersFormatted(array $headers): array
    {
        $headersFormatted = [];
        foreach ($headers as $name => $values) {
            $headersFormatted[$name] = implode(", ", $values);
        }

        return $headersFormatted;
    }
}