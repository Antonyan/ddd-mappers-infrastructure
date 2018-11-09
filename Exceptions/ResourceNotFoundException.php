<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceNotFoundException extends InternalException
{
    /**
     * ResourceNotFoundException constructor.
     * @param $message
     * @param array $data
     * @param int $errorCode
     * @param Throwable|null $previous
     */
    public function __construct($message, $data = [], $errorCode = self::DEFAULT_ERROR_CODE, Throwable $previous = null)
    {
        parent::__construct($message, Response::HTTP_NOT_FOUND, [], $data, $errorCode, $previous);
    }
}