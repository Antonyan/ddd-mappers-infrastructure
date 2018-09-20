<?php

namespace Infrastructure\Models\Http;


class UrlPlaceholderRender
{
    public const GET_URL = 'getUrl';
    public const GET_ONE_URL = 'getOneUrl';
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
     * @return string
     */
    public function getUrl(array $data = []): string
    {
        return $this->render(self::GET_URL, $data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function getOneUrl(array $data = []): string
    {
        return $this->render(self::GET_ONE_URL, $data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function createUrl(array $data = []): string
    {
        return $this->render(self::CREATE_URL, $data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function updateUrl(array $data = []): string
    {
        return $this->render(self::UPDATE_URL, $data);
    }

    /**
     * @param array $data
     * @return string
     */
    public function deleteUrl(array $data = []): string
    {
        return $this->render(self::DELETE_URL, $data);
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
}