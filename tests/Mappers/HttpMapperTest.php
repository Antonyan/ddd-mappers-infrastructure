<?php

namespace Infrastructure\Tests\Mappers;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Infrastructure\Mappers\HttpMapper;
use Infrastructure\Models\Http\Headers;
use Infrastructure\Models\Http\HttpClient;
use Infrastructure\Models\Http\RequestFactoryInterface;
use Infrastructure\Models\Http\Response\AbstractResponseDecorator;
use Infrastructure\Models\Http\UrlRender;
use Infrastructure\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @see HttpMapper
 */
class HttpMapperTest extends TestCase
{
    use ReflectionHelperTrait;

    /**
     * @throws \ReflectionException
     */
    public function test__construct()
    {
        $config = [
            HttpMapper::AVAILABLE_URLS => [
                UrlRender::GET_URL => 'get_url'
            ],
            HttpMapper::DEFAULT_HEADERS => [
                'Content-Type' => ['application/json']
            ],
        ];

        $httpClient = $this->createInstance(HttpClient::class);
        $requestFactory = new class implements RequestFactoryInterface {
            public function create($method, $uri, array $headers = [], $body = null): RequestInterface {}
        };

       $httpMapper = $this->getMockBuilder(HttpMapper::class)
           ->disableOriginalConstructor()
           ->getMockForAbstractClass();

       $this->callMethod($httpMapper, '__construct', $config, $httpClient, $requestFactory);

       $this->assertEquals($httpClient, $this->callMethod($httpMapper, 'getHttpClient'));
       $this->assertEquals($requestFactory, $this->getPrivatePropertyFromMock($httpMapper, 'requestFactory'));

       /** @var Headers $defaultHeaders */
       $defaultHeaders = $this->getPrivatePropertyFromMock($httpMapper, 'defaultHeaders');
       /** @var UrlRender $urlRender */
       $urlRender = $this->getPrivatePropertyFromMock($httpMapper, 'urlRender');

       $this->assertInstanceOf(Headers::class, $defaultHeaders);
       $this->assertInstanceOf(UrlRender::class, $urlRender);

       $this->assertEquals($config[HttpMapper::DEFAULT_HEADERS], $defaultHeaders->toArray());
       $this->assertEquals($config[HttpMapper::AVAILABLE_URLS], $this->getProperty($urlRender, 'urlsPlaceholders'));

    }

    /**
     * @see HttpMapper::sendRequest()
     *
     * @throws \ReflectionException
     */
    public function testSendRequest()
    {
        $sourceRequest = $this->createInstance(Request::class);
        $requestWithMergedDefaultData = $this->createInstance(Request::class);
        $response = $this->getMockBuilder(AbstractResponseDecorator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $httpClient = $this->getMockBuilder(HttpClient::class)
            ->disableOriginalConstructor()
            ->setMethods(['send'])
            ->getMock();

        $httpClient
            ->expects($this->once())
            ->method('send')
            ->with($requestWithMergedDefaultData)
            ->willReturn($response);

        $httpMapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHttpClient', 'mergeDefaultData'])
            ->getMockForAbstractClass();

        $httpMapper
            ->expects($this->once())
            ->method('getHttpClient')
            ->willReturn($httpClient);

        $httpMapper
            ->expects($this->once())
            ->method('mergeDefaultData')
            ->with($sourceRequest)
            ->willReturn($requestWithMergedDefaultData);


        $this->assertEquals($response, $this->callMethod($httpMapper, 'sendRequest', $sourceRequest));
    }

//    public function testDelete()
//    {
//
//    }
//
//    public function testUpdatePatch()
//    {
//
//    }
//
//    public function testLoad()
//    {
//
//    }
//
//    public function testUpdate()
//    {
//
//    }
//
//    public function testGet()
//    {
//
//    }
//
//    public function testCreate()
//    {
//
//    }
}
