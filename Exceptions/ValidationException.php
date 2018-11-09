<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ValidationException extends InternalException
{
    /**
     * ValidationException constructor.
     * @param string $message
     * @param int $errorCode
     * @param array $data
     */
    public function __construct(string $message, array $data = [], int $errorCode = self::VALIDATION_ERROR_CODE)
    {
        parent::__construct($message, Response::HTTP_BAD_REQUEST, $errorCode, [], $data);
    }
}