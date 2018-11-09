<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ClientErrorException extends InternalException
{
    /**
     * ClientErrorException constructor.
     * @param string $message
     * @param int $statusCode
     * @param array $data
     * @param int $errorCode
     */
    public function __construct(string $message, $statusCode = Response::HTTP_BAD_REQUEST, $data = [], $errorCode = self::DEFAULT_ERROR_CODE)
    {
        parent::__construct(
            $message,
            $statusCode,
            $errorCode,
            [],
            $data
        );
    }
}