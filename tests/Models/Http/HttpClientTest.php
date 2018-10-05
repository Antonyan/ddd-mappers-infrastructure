<?php

namespace Infrastructure\Tests\Models\Http;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\AppendStream;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Exceptions\InternalException;
use Infrastructure\Models\Http\HttpClient;
use Infrastructure\Models\Http\Response\JsonResponse;
use Infrastructure\Models\Http\Response\ResponseFactory;
use Infrastructure\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\TestCase;

/**
 * @see HttpClient
 */
class HttpClientTest extends TestCase
{
    use ReflectionHelperTrait;

    /**
     * @throws \ReflectionException
     */
    public function test__construct()
    {
        $httpClient = new HttpClient();

        $this->assertInstanceOf(Client::class, $this->getProperty($httpClient, 'guzzleHttpClient'));
        $this->assertInstanceOf(ResponseFactory::class, $this->getProperty($httpClient, 'responseFactory'));

        $anonymousClass = new class extends ResponseFactory {};

        $httpClient = new HttpClient($anonymousClass);

        $this->assertInstanceOf(Client::class, $this->getProperty($httpClient, 'guzzleHttpClient'));
        $this->assertInstanceOf(get_class($anonymousClass), $this->getProperty($httpClient, 'responseFactory'));
    }

    /**
     * @see HttpClient::send()
     *
     * @throws \Infrastructure\Exceptions\InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     * @throws \ReflectionException
     */
    public function testSend()
    {
        $request = $this->createMock(Request::class);
        $response = $this->createMock(Response::class);
        $jsonResponse = $this->createMock(JsonResponse::class);
        $guzzleClient = $this->getMockBuilder(Client::class)
            ->setMethods(['send'])
            ->getMock();
        $anonymousResponseFactory = $this->getMockBuilder(ResponseFactory::class)
            ->setMethods(['createFromResponse'])
            ->getMock();

        $anonymousResponseFactory
            ->expects($this->once())
            ->method('createFromResponse')
            ->with($response)
            ->willReturn($jsonResponse);

        $guzzleClient
            ->expects($this->once())
            ->method('send')
            ->with($request)
            ->willReturn($response);

        $httpClient = new HttpClient($anonymousResponseFactory);

        $this->setProperty($httpClient, 'guzzleHttpClient', $guzzleClient);

        $this->assertEquals($jsonResponse, $httpClient->send($request));
    }

    /**
     * @see          HttpClient::send()
     *
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     * @throws \ReflectionException
     */
    public function testSendInternalException()
    {
        /** @var RequestException $exception */
        $exception = $this->createInstance(RequestException::class);
        $response = $this->createInstance(Response::class);
        $this->setProperty($exception, 'response', $response);

        $this->expectException(InternalException::class);
        $this->provokeException($exception);
    }

    /**
     * @see          HttpClient::send()
     *
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     * @throws \ReflectionException
     */
    public function testSendInfrastructureException()
    {
        $exception = new /**
         * @method string getMessage()
         * @method null getPrevious()
         * @method mixed getCode()
         * @method string getFile()
         * @method int getLine()
         * @method array getTrace()
         * @method string getTraceAsString()
         */
        class extends Exception implements GuzzleException {
            public function __call($name, $arguments)
            {
            }
        };

        $this->expectException(InternalException::class);
        $this->provokeException(new $exception);

    }

    /**
     * @param $exception
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     * @throws \ReflectionException
     */
    protected function provokeException($exception): void
    {
        $guzzleClient = $this->getMockBuilder(Client::class)
            ->setMethods(['send'])
            ->getMock();

        $guzzleClient->method('send')->willThrowException($exception);

        $client = new HttpClient();

        $this->setProperty($client, 'guzzleHttpClient', $guzzleClient);


        $client->send($this->createInstance(Request::class));
    }
}
