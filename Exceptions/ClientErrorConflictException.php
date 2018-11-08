<?php

namespace Infrastructure\Exceptions;


use Symfony\Component\HttpFoundation\Response;

class ClientErrorConflictException extends ClientErrorException
{
    /**
     * ClientErrorConflictException constructor.
     * @param string $message
     * @param int $statusCode
     * @param int $errorCode
     * @param array $body
     */
    public function __construct(string $message, $statusCode = Response::HTTP_CONFLICT, $errorCode = self::DEFAULT_ERROR_CODE, $body = [])
    {
        parent::__construct(
            $message,
            $statusCode,
            $errorCode,
            [],
            $body
        );
    }
}