<?php

namespace Infrastructure\Models\Http\Response;


use Infrastructure\Models\Http\ResponseInterface;
use \Psr\Http\Message\ResponseInterface as PsrResponseInterface;

class ResponseFactory
{
    /**
     * @param PsrResponseInterface $response
     * @return ResponseInterface
     * @throws ResponseContentTypeException
     */
    public function createFromResponse(PsrResponseInterface $response): ResponseInterface
    {
        $contentType = $response->getHeader('Content-Type')[0] ?? '';

        foreach ($this->getContentTypeResponseMap($response) as $allowedContentType => $creatorResponse) {
            if ($this->isAllowedContentType($contentType, $allowedContentType)) {
                return $creatorResponse($response);
            }
        }

        throw new ResponseContentTypeException($contentType);
    }

    /**
     * @param PsrResponseInterface $response
     * @return array
     */
    private function getContentTypeResponseMap(PsrResponseInterface $response)
    {
        return [
            ResponseInterface::CONTENT_TYPE_JSON => function() use($response) {
                return $this->createJsonResponse($response);
            },
            ResponseInterface::CONTENT_TYPE_XML => function() use($response) {
                return $this->createXmlResponse($response);
            },
        ];
    }

    private function isAllowedContentType(string $contentType, $allowedContentType): bool
    {
        return strpos($allowedContentType, $contentType) !== false;
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