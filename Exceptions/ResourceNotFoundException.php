<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceNotFoundException extends InternalException
{
    /**
     * HttpResourceNotFoundException constructor.
     * @param $message
     * @param Throwable|null $previous
     * @param int $errorCode
     * @param array $body
     */
    public function __construct($message, $errorCode = self::DEFAULT_ERROR_CODE, $body = [], Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, [], $body, $errorCode, $previous);
    }
}