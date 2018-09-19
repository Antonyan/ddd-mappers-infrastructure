<?php
namespace Infrastructure\Mappers;

use Infrastructure\Exceptions\HttpResourceNotFoundException;
use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Exceptions\ValidationException;
use Infrastructure\Models\ArraySerializable;
use Infrastructure\Models\Collection;
use Infrastructure\Models\Http\HttpClient;
use Infrastructure\Models\Http\Request;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Models\SearchCriteria\SearchCriteria;

abstract class HttpMapper extends BaseMapper
{
    const LOAD_URL = 'loadUrl';
    const GET_URL = 'getUrl';
    const GET_ONE_URL = 'getOneUrl';
    const CREATE_URL = 'createUrl';
    const UPDATE_URL = 'updateUrl';
    const DELETE_URL = 'deleteUrl';

    public const NAME_OF_IDENTIFIER = 'nameOfIdentifier';

    const LINKS_FIELD = 'links';

    public const CONTENT_TYPE_JSON = 'application/json';

    /**
     * @var HttpClient
     */
    private $httpClient = null;

    /**
     * @var array
     */
    private $httpMapperConfig = null;

    /**
     * @var string
     */
    protected static $contentType = self::CONTENT_TYPE_JSON;

    /**
     * HttpMapper constructor.
     * @param array $httpMapperConfig
     * @param HttpClient $httpClient
     */
    public function __construct(array $httpMapperConfig, HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->httpMapperConfig = $httpMapperConfig;
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
        return $this->sendHttpRequest('POST', $this->createUrl(), [], $objectData);
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
        return $this->sendHttpRequest('PUT', $this->updateUrl($objectData), [], $objectData);
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
        $result = $this->sendHttpRequest('GET', $this->getUrl(), $params);

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
        return $this->sendHttpRequest('GET', $this->getOneUrl($identifiers));
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
        $deleteUrl = $this->getUrlWithIdentifier($propertyValue, $this->deleteUrl());
        return $this->sendHttpRequest('DELETE', $deleteUrl);
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
        return $this->sendHttpRequest('PATCH',$this->updateUrl($objectData), [], $objectData);
    }

    /**
     * @param $method
     * @param $url
     * @param $params
     * @param $body
     * @return Collection|mixed
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function sendHttpRequest($method, $url, $params = [], $body = null)
    {
        if (!empty($params)) {
            $separator = strpos($url, '?') === null ? '?' : '&';
            $url = trim($url, '&') . $separator . http_build_query($params, '&');
        }

        $response = $this->getHttpClient()->send(
            new Request(
                $method,
                $url,
                ['Content-Type' => self::$contentType],
                $body
            )
        );
        $contentTypeHeader = $response->getHeader('Content-Type');

        if ($response->getStatusCode() >= 400) {
            $this->throwException($response->getStatusCode());
        }

        if ($response->getBody()->eof())  {
            return true;
        }

        $contentAsArray = $this->parse($response->getBody()->getContents(), array_shift($contentTypeHeader));

        if (array_key_exists(Collection::ITEMS, $contentAsArray)) {
            return $this->buildCollection($contentAsArray[Collection::ITEMS]);
        }

        return $this->buildObject($contentAsArray);
    }

    /**
     * @param $identifierValue
     * @param $url
     * @return string
     */
    private function getUrlWithIdentifier($identifierValue, $url)
    {
        $urlTemplate = $this->getHttpMapperConfig($url);
        return vsprintf($urlTemplate, [$identifierValue]);
    }

    /**
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param string $content
     * @param $typeContent
     * @return array
     * @throws InfrastructureException
     */
    private function parse(string $content, $typeContent)
    {
        if ($typeContent == self::CONTENT_TYPE_JSON) {
            return json_decode($content, true);
        }

        throw new InfrastructureException("Illegal Content-Type: " . $typeContent);
    }

    /**
     * @param $urlName
     * @return mixed
     */
    protected function getHttpMapperConfig($urlName)
    {
        return $this->httpMapperConfig[$urlName];
    }

    /**
     * @param int $statusCode
     * @throws HttpResourceNotFoundException
     * @throws InfrastructureException
     * @throws ValidationException
     */
    private function throwException(int $statusCode)
    {
        switch (true) {
            case $statusCode == 404:
                throw new HttpResourceNotFoundException('Http resource not found');

                break;
            case $statusCode >= 400 && $statusCode < 500:
                throw new ValidationException([]);

                break;
            case $statusCode >= 500:
                throw new InfrastructureException('InfrastructureException');

                break;
        }
    }

    /**
     * @return string
     */
    protected function getUrl(): string
    {
        return $this->getHttpMapperConfig(self::GET_URL);
    }

    /**
     * @param array $identifiers
     * @return string
     */
    protected function getOneUrl(array $identifiers)
    {
        $objectId = $identifiers[$this->getHttpMapperConfig(self::NAME_OF_IDENTIFIER)];

        return $this->getUrlWithIdentifier(
            $objectId,
            $this->getHttpMapperConfig(self::GET_ONE_URL));
    }

    /**
     * @return string
     */
    protected function createUrl(): string
    {
        return $this->getHttpMapperConfig(self::CREATE_URL);
    }

    /**
     * @param array $objectData
     * @return string
     */
    protected function updateUrl(array $objectData): string
    {
        $objectId = $objectData[$this->getHttpMapperConfig(self::NAME_OF_IDENTIFIER)];

        return $this->getUrlWithIdentifier(
            $objectId,
            $this->getHttpMapperConfig(self::UPDATE_URL));
    }

    /**
     * @return string
     */
    protected function deleteUrl(): string
    {
        return $this->getHttpMapperConfig(self::DELETE_URL);
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
}