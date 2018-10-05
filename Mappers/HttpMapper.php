<?php
namespace Infrastructure\Mappers;

use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Exceptions\InternalException;
use Infrastructure\Models\ArraySerializable;
use Infrastructure\Models\Collection;
use Infrastructure\Models\Http\RequestFactoryInterface;
use Infrastructure\Models\Http\Headers;
use Infrastructure\Models\Http\HttpClient;
use Infrastructure\Models\Http\UrlRender;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Models\SearchCriteria\SearchCriteria;
use Infrastructure\Services\BaseFactory;
use Psr\Http\Message\RequestInterface;

abstract class HttpMapper extends BaseMapper
{
    public const LINKS_FIELD = 'links';

    public const AVAILABLE_URLS = 'availableUrls';
    public const DEFAULT_HEADERS = 'defaultHeaders';

    protected const GET = 'GET';
    protected const POST = 'POST';
    protected const PUT = 'PUT';
    protected const PATCH = 'PATCH';
    protected const DELETE = 'DELETE';

    /**
     * @var HttpClient
     */
    private $httpClient = null;

    /**
     * @var BaseFactory
     */
    private $factory;

    /**
     * @var Headers
     */
    private $defaultHeaders;

    /**
     * @var RequestFactoryInterface
     */
    private $requestFactory;

    /**
     * @var UrlRender
     */
    private $urlRender;

    /**
     * HttpMapper constructor.
     *
     * @param array $httpMapperConfig
     * @param HttpClient $httpClient
     * @param RequestFactoryInterface $requestFactory
     * @param BaseFactory $factory
     *
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     */
    public function __construct(
        array $httpMapperConfig,
        HttpClient $httpClient,
        RequestFactoryInterface $requestFactory,
        BaseFactory $factory
    )
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
        $this->factory = $factory;

        $this->defaultHeaders = new Headers($httpMapperConfig[self::DEFAULT_HEADERS] ?? []);
        $this->urlRender = new UrlRender($httpMapperConfig[self::AVAILABLE_URLS]);
    }

    /**
     * @param SearchCriteria $filter
     * @return PaginationCollection
     * @throws InfrastructureException
     * @throws InternalException
     */
    public function load(SearchCriteria $filter): PaginationCollection
    {
        $params = $this->prepareParams($filter);

        $result = $this->sendRequestForCollection($this->requestFactory->create(
            self::GET,
            $this->urlRender->prepareLoadUrl([], $params)
        ));

        $paginationCollection = new PaginationCollection(count($result), $filter->limit(), $filter->offset());
        $paginationCollection->merge($result);

        return $paginationCollection;
    }

    /**
     * @param array $identifiers
     * @return ArraySerializable
     * @throws InfrastructureException
     * @throws InternalException
     */
    public function get(array $identifiers)
    {
        return $this->sendRequestForEntity(
            $this->requestFactory->create(self::GET, $this->urlRender->prepareGetUrl($identifiers))
        );
    }

    /**
     * @param array $objectData
     * @return ArraySerializable
     * @throws InfrastructureException
     * @throws InternalException
     */
    public function create(array $objectData)
    {
        return $this->createObject($objectData);
    }

    /**
     * @param array $objectData
     * @return ArraySerializable
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    public function update(array $objectData)
    {
        return $this->sendRequestForEntity(
            $this->requestFactory->create(self::PUT, $this->urlRender->prepareUpdateUrl($objectData), [], $objectData)
        );
    }

    /**
     * @param string $byPropertyName
     * @param $propertyValue
     * @return bool
     * @throws InfrastructureException
     * @throws InternalException
     */
    public function delete(string $byPropertyName, $propertyValue): bool
    {
        $this->sendRequest($this->requestFactory->create(
            self::DELETE, $this->urlRender->prepareDeleteUrl([$byPropertyName => $propertyValue])
        ));

        return true;
    }

    /**
     * @param array $objectData
     * @return Collection|mixed
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    public function updatePatch(array $objectData)
    {
        return $this->sendRequestForEntity(
            $this->requestFactory->create(self::PATCH, $this->urlRender->prepareUpdateUrl($objectData), [], $objectData)
        );
    }

    /**
     * @param RequestInterface $request
     * @return \Infrastructure\Models\Http\ResponseInterface
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    protected function sendRequest(RequestInterface $request)
    {
        return $this->getHttpClient()->send($this->mergeDefaultData($request));
    }

    /**
     * @param RequestInterface $request
     * @return ArraySerializable
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    protected function sendRequestForEntity(RequestInterface $request): ArraySerializable
    {
        return $this->buildObject($this->sendRequest($request)->getParsedBody());
    }

    /**
     * @param RequestInterface $request
     * @return Collection
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    protected function sendRequestForCollection(RequestInterface $request): Collection
    {
        return $this->buildCollection($this->sendRequest($request)->getParsedBody());
    }

    /**
     * @param array $objectData
     * @return ArraySerializable
     */
    protected function buildObject(array $objectData) : ArraySerializable
    {
        return $this->factory->create($objectData);
    }

    /**
     * @param array $data
     *
     * @return ArraySerializable
     *
     * @throws InternalException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    protected function createObject(array $data): ArraySerializable
    {
        return $this->sendRequestForEntity(
            $this->requestFactory->create(self::POST, $this->urlRender->prepareCreateUrl($data), [], $data)
        );
    }

    /**
     * @param array $data
     *
     * @return ArraySerializable
     *
     * @throws InfrastructureException
     */
    protected function updateObject(array $data): ArraySerializable
    {
        throw new InfrastructureException('the method is not supported');
    }

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param SearchCriteria $filter
     * @return array
     */
    private function prepareParams(SearchCriteria $filter): array
    {
        $params = array_merge([
            SearchCriteria::LIMIT => $filter->limit(),
            SearchCriteria::OFFSET => $filter->offset()
        ], $filter->orderBy());

        $params = array_merge($params, $filter->conditions());
        return $params;
    }

    /**
     * @param RequestInterface $request
     * @return RequestInterface
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     */
    private function mergeDefaultData(RequestInterface $request)
    {
        return $this->requestFactory->create(
            $request->getMethod(),
            $request->getUri(),
            $this->defaultHeaders->merge(new Headers($request->getHeaders())),
            $request->getBody()
        );
    }
}