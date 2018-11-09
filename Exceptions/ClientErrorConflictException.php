<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ClientErrorConflictException extends ClientErrorException
{
    /**
     * ClientErrorConflictException constructor.
     * @param string $message
     * @param int $errorCode
     * @param array $data
     */
    public function __construct(string $message, $data = [], $errorCode = self::DEFAULT_ERROR_CODE)
    {
        parent::__construct(
            $message,
            Response::HTTP_CONFLICT,
            $errorCode,
            [],
            $data
        );
    }
}