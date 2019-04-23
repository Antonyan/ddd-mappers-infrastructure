<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\ErrorData;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ResourceNotFoundException extends InternalException
{
    /**
     * ResourceNotFoundException constructor.
     * @param string         $message
     * @param int            $errorCode
     * @param ErrorData      $data
     * @param Throwable|null $previous
     */
    public function __construct(string $message, int $errorCode = self::DEFAULT_ERROR_CODE, ErrorData $data = null, Throwable $previous = null)
    {
        parent::__construct($message, $errorCode, $data, Response::HTTP_NOT_FOUND, new ErrorData(), $previous);
    }
}