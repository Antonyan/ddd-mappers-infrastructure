<?php

namespace Infrastructure\Models\Http\Response;


use Infrastructure\Models\Http\ResponseInterface;
use \Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class ResponseFactory
{
    /**
     * @var array
     */
    private static $mapper = [
        /** @see ResponseFactory::createJsonResponse() */
        ResponseInterface::CONTENT_TYPE_JSON => [ResponseFactory::class, 'createJsonResponse'],
        /** @see ResponseFactory::createXmlResponse() */
        ResponseInterface::CONTENT_TYPE_XML => [ResponseFactory::class, 'createXmlResponse'],
    ];

    /**
     * @param PsrResponseInterface $response
     * @return ResponseInterface
     * @throws ResponseContentTypeException
     */
    public function createFromResponse(PsrResponseInterface $response): ResponseInterface
    {
        $contentType = $response->getHeader('Content-Type')[0] ?? '';

        if (!in_array($contentType, array_keys(self::$mapper))) {
            throw new ResponseContentTypeException($contentType);
        }

        return call_user_func(self::$mapper[$contentType], $response);
    }

    /**
     * @param PsrResponseInterface $response
     * @return JsonResponse
     */
    private function createJsonResponse(PsrResponseInterface $response): JsonResponse
    {
        return new JsonResponse($response);
    }

    /**
     * @param PsrResponseInterface $response
     * @throws ResponseContentTypeException
     */
    private function createXmlResponse(PsrResponseInterface $response)
    {
        throw new ResponseContentTypeException(ResponseInterface::CONTENT_TYPE_XML);
    }
}