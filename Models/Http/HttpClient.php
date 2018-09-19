<?php

namespace Infrastructure\Models\Http;


use GuzzleHttp\Client;
use \Psr\Http\Message\ResponseInterface;

class HttpClient
{
    /**
     * @var Client
     */
    private $guzzleHttpClient;

    public function __construct()
    {
        $this->guzzleHttpClient = new Client();
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(Request $request): Response
    {
        $psrResponse = $this->guzzleHttpClient->send(
            $this->createGuzzleRequest($request)
        );

        return $this->createResponseFromGazzle($psrResponse);
    }

    /**
     * @param Request $request
     * @return \GuzzleHttp\Psr7\Request
     */
    private function createGuzzleRequest(Request $request): \GuzzleHttp\Psr7\Request
    {
        return new \GuzzleHttp\Psr7\Request(
            $request->getMethod(),
            $request->getUri(),
            $request->getHeaders(),
            $request->getBody(),
            $request->getProtocolVersion()
        );
    }

    /**
     * @param ResponseInterface $psrResponse
     * @return Response
     */
    private function createResponseFromGazzle(ResponseInterface $psrResponse): Response
    {
        return new Response(
            $psrResponse->getStatusCode(),
            $psrResponse->getHeaders(),
            $psrResponse->getBody(),
            $psrResponse->getProtocolVersion(),
            $psrResponse->getReasonPhrase()
        );
    }
}