<?php

namespace Infrastructure\Models\Http;


class UrlRender
{
    public const LOAD_URL = 'loadUrl';
    public const GET_URL = 'getUrl';
    public const CREATE_URL = 'createUrl';
    public const UPDATE_URL = 'updateUrl';
    public const DELETE_URL = 'deleteUrl';

    /**
     * @var array
     */
    private $urlsPlaceholders;

    /**
     * UrlPlaceholderRender constructor.
     * @param array $urlsPlaceholders
     */
    public function __construct(array $urlsPlaceholders)
    {
        $this->urlsPlaceholders = $urlsPlaceholders;
    }

    /**
     * @param array $data
     * @param array $params
     * @return string
     */
    public function prepareLoadUrl(array $data = [], array $params = []): string
    {
        return $this->buildUrl($this->render(self::LOAD_URL, $data), $params);
    }

    /**
     * @param array $data
     * @param array $params
     * @return string
     */
    public function prepareGetUrl(array $data = [], array $params = []): string
    {
        return $this->buildUrl($this->render(self::GET_URL, $data), $params);
    }

    /**
     * @param array $data
     * @param array $params
     * @return string
     */
    public function prepareCreateUrl(array $data = [], array $params = []): string
    {
        return $this->buildUrl($this->render(self::CREATE_URL, $data), $params);
    }

    /**
     * @param array $data
     * @param array $params
     * @return string
     */
    public function prepareUpdateUrl(array $data = [], array $params = []): string
    {
        return $this->buildUrl($this->render(self::UPDATE_URL, $data), $params);
    }

    /**
     * @param array $data
     * @param array $params
     * @return string
     */
    public function prepareDeleteUrl(array $data = [], array $params = []): string
    {
        return $this->buildUrl($this->render(self::DELETE_URL, $data), $params);
    }

    /**
     * @param string $key
     * @param array $data
     * @return string
     */
    public function render(string $key, array $data = []): string
    {
        return strtr(
            $this->urlsPlaceholders[$key],
            array_combine($this->extractPlaceholders($data), array_values($data))
        );
    }

    /**
     * @param array $data
     * @return array
     */
    private function extractPlaceholders(array $data = []): array
    {
        return array_map(function ($propertyName) {return ':' . $propertyName;}, array_keys($data));
    }

    /**
     * @param string $url
     * @param array $params
     * @return string
     */
    private function buildUrl(string $url, array $params = [])
    {
        $url = trim($url, '&?');
        if (!empty($params)) {
            $separator = strpos($url, '?') === false ? '?' : '&';
            $url = $url . $separator . http_build_query($params);
        }
        return $url;
    }
}