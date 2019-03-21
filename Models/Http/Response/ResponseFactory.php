<?php

namespace Infrastructure\Models\Http\Response;


use Infrastructure\Models\Http\ResponseInterface;
use \Psr\Http\Message\ResponseInterface as PsrResponseInterface;
use Symfony\Component\HttpFoundation\Response;

class ResponseFactory
{
    /**
     * @param PsrResponseInterface $response
     * @return ResponseInterface
     */
    public function createFromResponse(PsrResponseInterface $response): ResponseInterface
    {
        $contentType = $response->getHeader('Content-Type')[0] ?? '';

        if ($response->getStatusCode() == Response::HTTP_NO_CONTENT) {
            return new EmptyResponse($response);
        }

        foreach ($this->getContentTypeResponseMap($response) as $allowedContentType => $creatorResponse) {
            if ($this->isAllowedContentType($contentType, $allowedContentType)) {
                return $creatorResponse($response);
            }
        }

        return new RawResponse($response);
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
        return stripos($contentType, $allowedContentType) !== false;
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