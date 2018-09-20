<?php
namespace Infrastructure\Mappers;

use Infrastructure\Exceptions\HttpResourceNotFoundException;
use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Exceptions\ValidationException;
use Infrastructure\Models\ArraySerializable;
use Infrastructure\Models\Collection;
use Infrastructure\Models\Http\AbstractRequestFactory;
use Infrastructure\Models\Http\HeadersBuilder;
use Infrastructure\Models\Http\HttpClient;
use Infrastructure\Models\Http\UrlPlaceholderRender;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Models\SearchCriteria\SearchCriteria;

abstract class HttpMapper extends BaseMapper
{
    public const LINKS_FIELD = 'links';

    public const CONTENT_TYPE_JSON = 'application/json';

    public const AVAILABLE_URLS = 'availableUrls';
    public const DEFAULT_HEADERS = 'defaultHeaders';

    protected const GET = 'GET';
    protected const POST = 'POST';
    protected const PUT = 'PUT';
    protected const PATH = 'PATH';
    protected const DELETE = 'DELETE';

    /**
     * @var string
     */
    protected static $defaultContentType = self::CONTENT_TYPE_JSON;

    /**
     * @var HttpClient
     */
    private $httpClient = null;

    /**
     * @var HeadersBuilder
     */
    private $headersBuilder;

    /**
     * @var AbstractRequestFactory
     */
    private $requestFactory;

    /**
     * @var UrlPlaceholderRender
     */
    private $urlPlaceholderRender;

    /**
     * HttpMapper constructor.
     * @param array $httpMapperConfig
     * @param HttpClient $httpClient
     * @param AbstractRequestFactory $requestFactory
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     */
    public function __construct(array $httpMapperConfig, HttpClient $httpClient, AbstractRequestFactory $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;

        $this->headersBuilder = new HeadersBuilder($httpMapperConfig[self::DEFAULT_HEADERS] ?? []);
        $this->urlPlaceholderRender = new UrlPlaceholderRender($httpMapperConfig[self::AVAILABLE_URLS]);
    }

    /**
     * @param SearchCriteria $filter
     * @return PaginationCollection
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function load(SearchCriteria $filter): PaginationCollection
    {
        $params = $this->prepareParams($filter);
        $result = $this->sendHttpRequest(self::GET, $this->urlPlaceholderRender->getUrl(), $params);

        $paginationCollection = new PaginationCollection(count($result), $filter->limit(), $filter->offset());
        $paginationCollection->merge($result);

        return $paginationCollection;
    }

    /**
     * @param array $identifiers
     * @return ArraySerializable
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(array $identifiers) : ArraySerializable
    {
        return $this->sendHttpRequest(self::GET, $this->urlPlaceholderRender->getOneUrl($identifiers));
    }

    /**
     * @param array $objectData
     * @return ArraySerializable
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $objectData): ArraySerializable
    {
        return $this->sendHttpRequest(self::POST, $this->urlPlaceholderRender->createUrl($objectData), [], $objectData);
    }

    /**
     * @param array $objectData
     * @return PaginationCollection|mixed
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(array $objectData)
    {
        return $this->sendHttpRequest(self::PUT, $this->urlPlaceholderRender->updateUrl($objectData), [], $objectData);
    }

    /**
     * @param string $byPropertyName
     * @param $propertyValue
     * @return bool
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $byPropertyName, $propertyValue): bool
    {
        $this->sendHttpRequest(self::DELETE, $this->urlPlaceholderRender->deleteUrl([$byPropertyName => $propertyValue]));

        return true;
    }


    /**
     * @param array $objectData
     * @return Collection|mixed
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function updatePatch(array $objectData)
    {
        return $this->sendHttpRequest(self::PATH, $this->urlPlaceholderRender->updateUrl($objectData), [], $objectData);
    }

    /**
     * @param $method
     * @param $url
     * @param array $params
     * @param $body
     * @return Collection|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     * @throws \Infrastructure\Models\Http\Response\ResponseContentTypeException
     */
    protected function sendHttpRequest($method, $url, $params = [], $body = null)
    {
        if (!$this->headersBuilder->hasHeader('Content-Type')) {
            $this->headersBuilder->addHeader('Content-Type', self::$defaultContentType);
        }

        $request = $this->requestFactory->create(
            $method,
            $this->buildUrl($url, $params),
            $this->headersBuilder->build(),
            $body
        );

        $response = $this->getHttpClient()->send($request);

        if ($response->getStatusCode() >= 400) {
            return $this->errorHandler()->handle($response);
        }

        if ($response->getBody()->eof()) {
            return true;
        }

        $content = $response->getParsedBody();

        if (array_key_exists(Collection::ITEMS, $content)) {
            return $this->buildCollection($content[Collection::ITEMS]);
        }

        return $this->buildObject($content);
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
     * @param string $url
     * @param array $params
     * @return string
     */
    private function buildUrl(string $url, array $params = [])
    {
        if (!empty($params)) {
            $separator = strpos($url, '?') === null ? '?' : '&';
            $url = trim($url, '&') . $separator . http_build_query($params, '&');
        }

        return $url;
    }

    /**
     * @return HttpMapperErrorHandler
     */
    abstract protected function errorHandler(): HttpMapperErrorHandler;
}