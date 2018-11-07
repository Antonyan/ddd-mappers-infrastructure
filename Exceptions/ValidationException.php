<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ValidationException extends InternalException
{
    /**
     * ValidationException constructor.
     * @param string $message
     * @param int $errorCode
     * @param array $body
     */
    public function __construct(string $message, array $body = [], int $errorCode = self::VALIDATION_ERROR_CODE)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $errorCode, [], $body);
    }
}