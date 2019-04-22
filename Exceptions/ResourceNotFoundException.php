<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\StringMap;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceNotFoundException extends InternalException
{
    /**
     * ResourceNotFoundException constructor.
     * @param string         $message
     * @param int            $errorCode
     * @param StringMap      $data
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $errorCode = self::DEFAULT_ERROR_CODE, StringMap $data = null, Throwable $previous = null)
    {
        parent::__construct($message, $errorCode, $data, Response::HTTP_NOT_FOUND, new StringMap(), $previous);
    }
}