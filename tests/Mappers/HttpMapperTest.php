<?php

namespace Infrastructure\Tests\Mappers;

use GuzzleHttp\Psr7\Request;
use Infrastructure\Mappers\HttpMapper;
use Infrastructure\Models\Collection;
use Infrastructure\Models\Http\GuzzleRequestFactory;
use Infrastructure\Models\Http\Headers;
use Infrastructure\Models\Http\HttpClient;
use Infrastructure\Models\Http\RequestFactoryInterface;
use Infrastructure\Models\Http\Response\AbstractResponseDecorator;
use Infrastructure\Models\Http\Response\JsonResponse;
use Infrastructure\Models\Http\UrlRender;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Models\SearchCriteria\EqualCriteria;
use Infrastructure\Models\SearchCriteria\InArrayCriteria;
use Infrastructure\Models\SearchCriteria\OrderBy;
use Infrastructure\Models\SearchCriteria\SearchCriteria;
use Infrastructure\Models\SearchCriteria\SearchCriteriaConstructor;
use Infrastructure\Tests\ArraySerializableHelperTrait;
use Infrastructure\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * @see HttpMapper
 */
class HttpMapperTest extends TestCase
{
    use ReflectionHelperTrait;
    use ArraySerializableHelperTrait;

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
        $requestFactory = new class implements RequestFactoryInterface
        {
            public function create($method, $uri, array $headers = [], $body = null): RequestInterface
            {
            }
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

    /**
     * @see HttpMapper::prepareParams()
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testPrepareParams()
    {
        $testCondition = [
            new EqualCriteria('testScalar', 'value'),
            new InArrayCriteria('testArray', ['value1', 'value2', 'value3']),
        ];

        $limit = random_int(1, 100);
        $offset = random_int(1, 100);
        $orderProperty = 'id';
        $orderBy = new OrderBy(SearchCriteria::ORDER_DESCENDING, $orderProperty);

        $searchCriteria = new SearchCriteriaConstructor(
            $testCondition,
            $limit,
            $offset,
            $orderBy
        );

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $actual = $this->callPrivateMethodFromMock($mapper, 'prepareParams', $searchCriteria);

        $this->assertEquals([
            SearchCriteria::LIMIT => $limit,
            SearchCriteria::OFFSET => $offset,
            $orderProperty => SearchCriteria::ORDER_DESCENDING,
            SearchCriteria::WHERE_EQUAL_SIGN => ['testScalar' => 'value'],
            SearchCriteria::WHERE_IN => ['testArray' => ['value1', 'value2', 'value3']]
        ], $actual);
    }

    /**
     * @see HttpMapper::load()
     *
     * @throws \Infrastructure\Exceptions\InfrastructureException
     * @throws \Infrastructure\Exceptions\InternalException
     * @throws \ReflectionException
     * @throws \Exception
     */
    public function testLoad()
    {
        #region init value
        $testCondition = [
            new EqualCriteria('testScalar', 'value'),
            new InArrayCriteria('testArray', ['value1', 'value2', 'value3']),
        ];

        $limit = random_int(1, 100);
        $offset = random_int(1, 100);
        $orderProperty = 'id';
        $orderBy = new OrderBy(SearchCriteria::ORDER_DESCENDING, $orderProperty);
        $url = 'https://some-url.com';

        $searchCriteria = new SearchCriteriaConstructor(
            $testCondition,
            $limit,
            $offset,
            $orderBy
        );

        $result = new Collection([
            $this->createArraySerializableObject([uniqid()]),
            $this->createArraySerializableObject([uniqid()]),
        ]);

        $request = $this->createMock(Request::class);

        #endregion

        #region setting mocks

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequestForCollection'])
            ->getMockForAbstractClass();
        $requestFactory = $this->getMockBuilder(GuzzleRequestFactory::class)
            ->setMethods(['create'])
            ->getMock();
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepareLoadUrl'])
            ->getMock();

        $urlRender
            ->expects($this->once())
            ->method('prepareLoadUrl')
            ->with(
                $this->equalTo([]),
                $this->callMethod($mapper, 'prepareParams', $searchCriteria)
            )
            ->willReturn($url);

        $requestFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo($url)
            )
            ->willReturn($request);

        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);
        $this->setPrivatePropertyInMock($mapper, 'urlRender', $urlRender);

        $mapper
            ->expects($this->once())
            ->method('sendRequestForCollection')
            ->with($request)
            ->willReturn($result);
        #endregion

        $expected = (new PaginationCollection(count($result), $limit, $offset))->merge($result);

        /** @var $mapper HttpMapper */
        $this->assertEquals(
            $expected,
            $mapper->load($searchCriteria)
        );

    }

    /**
     * @see HttpMapper::sendRequestForEntity()
     *
     * @throws \ReflectionException
     */
    public function testSendRequestForEntity()
    {
        $result = [uniqid(), uniqid(), uniqid()];
        $request = $this->createMock(Request::class);
        $response = $this->getMockBuilder(JsonResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParsedBody'])
            ->getMock();

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest', 'buildObject'])
            ->getMockForAbstractClass();

        $response
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($result);

        $mapper
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($request))
            ->willReturn($response);

        $mapper
            ->expects($this->once())
            ->method('buildObject')
            ->with($this->equalTo($result))
            ->willReturn($this->createArraySerializableObject($result));

        $this->assertEquals(
            $this->createArraySerializableObject($result),
            $this->callMethod($mapper, 'sendRequestForEntity', $request)
        );

    }

    /**
     * @see HttpMapper::sendRequestForCollection()
     *
     * @throws \ReflectionException
     */
    public function testSendRequestForCollection()
    {
        $result = [[uniqid()], [uniqid()], [uniqid()]];
        $request = $this->createMock(Request::class);
        $response = $this->getMockBuilder(JsonResponse::class)
            ->disableOriginalConstructor()
            ->setMethods(['getParsedBody'])
            ->getMock();

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest', 'buildCollection'])
            ->getMockForAbstractClass();

        $response
            ->expects($this->once())
            ->method('getParsedBody')
            ->willReturn($result);

        $mapper
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($request))
            ->willReturn($response);

        $mapper
            ->expects($this->once())
            ->method('buildCollection')
            ->with($this->equalTo($result))
            ->willReturn($this->createArraySerializableCollection($result));

        $this->assertEquals(
            $this->createArraySerializableCollection($result),
            $this->callMethod($mapper, 'sendRequestForCollection', $request)
        );

    }

    /**
     * @see HttpMapper::mergeDefaultData()
     *
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     * @throws \ReflectionException
     */
    public function testMergeDefaultData()
    {
        $method = 'PUT';
        $uri = 'https://test.com';
        $defaultHeaders = new Headers(['Content-Type' => 'application/json']);
        $headers = ['accept-language' => ['en-US,en;', 'q=0.9']];
        $body = [uniqid()];

        $mapper = $this->createMock(HttpMapper::class);
        $request = new Request($method, $uri, $headers, json_encode($body));
        $requestFactory = $this->createInstance(GuzzleRequestFactory::class);

        $this->setPrivatePropertyInMock($mapper, 'defaultHeaders', $defaultHeaders);
        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);

        /** @var  $mergedRequest RequestInterface */
        $mergedRequest = $this->callMethod($mapper, 'mergeDefaultData', $request);

        $this->assertEquals($request->getMethod(), $mergedRequest->getMethod(), 'Message: ' . $mergedRequest->getMethod() );
        $this->assertEquals($request->getUri(), $mergedRequest->getUri());
        $this->assertEquals($request->getBody(), $mergedRequest->getBody());
        $this->assertEquals(
            $defaultHeaders->merge(new Headers($request->getHeaders()))->toArray(),
            $mergedRequest->getHeaders()
        );

    }

    /**
     * @throws \Infrastructure\Exceptions\InfrastructureException
     * @throws \Infrastructure\Exceptions\InternalException
     * @throws \ReflectionException
     */
    public function testGet()
    {
        $request = $this->createMock(Request::class);
        $url = 'https://test-url.com';
        $identifiers = ['id' => uniqid()];
        $result = $this->createArraySerializableObject([uniqid()]);

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequestForEntity'])
            ->getMockForAbstractClass();

        $requestFactory = $this->getMockBuilder(GuzzleRequestFactory::class)
            ->setMethods(['create'])
            ->getMock();
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepareGetUrl'])
            ->getMock();

        $urlRender
            ->expects($this->once())
            ->method('prepareGetUrl')
            ->with($this->equalTo($identifiers))
            ->willReturn($url);

        $requestFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('GET'),
                $this->equalTo($url)
            )
            ->willReturn($request);

        $mapper
            ->expects($this->once())
            ->method('sendRequestForEntity')
            ->with($this->equalTo($request))
            ->willReturn($result);

        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);
        $this->setPrivatePropertyInMock($mapper, 'urlRender', $urlRender);

        /** @var $mapper HttpMapper */
        $this->assertEquals($result, $mapper->get($identifiers));
    }

    /**
     * @throws \Infrastructure\Exceptions\InfrastructureException
     * @throws \Infrastructure\Exceptions\InternalException
     * @throws \ReflectionException
     */
    public function testCreate()
    {
        $request = $this->createMock(Request::class);
        $url = 'https://test-url.com';
        $objectData = ['property' => uniqid()];
        $result = $this->createArraySerializableObject([uniqid()]);

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequestForEntity'])
            ->getMockForAbstractClass();

        $requestFactory = $this->getMockBuilder(GuzzleRequestFactory::class)
            ->setMethods(['create'])
            ->getMock();
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepareCreateUrl'])
            ->getMock();

        $urlRender
            ->expects($this->once())
            ->method('prepareCreateUrl')
            ->with($this->equalTo($objectData))
            ->willReturn($url);

        $requestFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('POST'),
                $this->equalTo($url),
                $this->equalTo([]),
                $this->equalTo($objectData)
            )
            ->willReturn($request);

        $mapper
            ->expects($this->once())
            ->method('sendRequestForEntity')
            ->with($this->equalTo($request))
            ->willReturn($result);

        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);
        $this->setPrivatePropertyInMock($mapper, 'urlRender', $urlRender);

        /** @var $mapper HttpMapper */
        $this->assertEquals($result, $mapper->create($objectData));
    }

    public function testUpdate()
    {
        $request = $this->createMock(Request::class);
        $url = 'https://test-url.com';
        $objectData = ['property' => uniqid()];
        $result = $this->createArraySerializableObject([uniqid()]);

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequestForEntity'])
            ->getMockForAbstractClass();

        $requestFactory = $this->getMockBuilder(GuzzleRequestFactory::class)
            ->setMethods(['create'])
            ->getMock();
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepareUpdateUrl'])
            ->getMock();

        $urlRender
            ->expects($this->once())
            ->method('prepareUpdateUrl')
            ->with($this->equalTo($objectData))
            ->willReturn($url);

        $requestFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('PUT'),
                $this->equalTo($url),
                $this->equalTo([]),
                $this->equalTo($objectData)
            )
            ->willReturn($request);

        $mapper
            ->expects($this->once())
            ->method('sendRequestForEntity')
            ->with($this->equalTo($request))
            ->willReturn($result);

        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);
        $this->setPrivatePropertyInMock($mapper, 'urlRender', $urlRender);

        /** @var $mapper HttpMapper */
        $this->assertEquals($result, $mapper->update($objectData));
    }

    public function testUpdatePatch()
    {
        $request = $this->createMock(Request::class);
        $url = 'https://test-url.com';
        $objectData = ['property' => uniqid()];
        $result = $this->createArraySerializableObject([uniqid()]);

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequestForEntity'])
            ->getMockForAbstractClass();

        $requestFactory = $this->getMockBuilder(GuzzleRequestFactory::class)
            ->setMethods(['create'])
            ->getMock();
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepareUpdateUrl'])
            ->getMock();

        $urlRender
            ->expects($this->once())
            ->method('prepareUpdateUrl')
            ->with($this->equalTo($objectData))
            ->willReturn($url);

        $requestFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('PATH'),
                $this->equalTo($url),
                $this->equalTo([]),
                $this->equalTo($objectData)
            )
            ->willReturn($request);

        $mapper
            ->expects($this->once())
            ->method('sendRequestForEntity')
            ->with($this->equalTo($request))
            ->willReturn($result);

        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);
        $this->setPrivatePropertyInMock($mapper, 'urlRender', $urlRender);

        /** @var $mapper HttpMapper */
        $this->assertEquals($result, $mapper->updatePatch($objectData));
    }


    /**
     * @throws \Infrastructure\Exceptions\InfrastructureException
     * @throws \Infrastructure\Exceptions\InternalException
     * @throws \ReflectionException
     */
    public function testDelete()
    {
        $request = $this->createMock(Request::class);
        $url = 'https://test-url.com';
        $objectData = ['property' => uniqid()];
        $result = $this->createArraySerializableObject([uniqid()]);

        $mapper = $this->getMockBuilder(HttpMapper::class)
            ->disableOriginalConstructor()
            ->setMethods(['sendRequest'])
            ->getMockForAbstractClass();

        $requestFactory = $this->getMockBuilder(GuzzleRequestFactory::class)
            ->setMethods(['create'])
            ->getMock();
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepareDeleteUrl'])
            ->getMock();

        $urlRender
            ->expects($this->once())
            ->method('prepareDeleteUrl')
            ->with($this->equalTo($objectData))
            ->willReturn($url);

        $requestFactory
            ->expects($this->once())
            ->method('create')
            ->with(
                $this->equalTo('DELETE'),
                $this->equalTo($url)
            )
            ->willReturn($request);

        $mapper
            ->expects($this->once())
            ->method('sendRequest')
            ->with($this->equalTo($request))
            ->willReturn($result);

        $this->setPrivatePropertyInMock($mapper, 'requestFactory', $requestFactory);
        $this->setPrivatePropertyInMock($mapper, 'urlRender', $urlRender);

        /** @var $mapper HttpMapper */
        $this->assertTrue(true, $mapper->delete('property', $objectData['property']));
    }

}
