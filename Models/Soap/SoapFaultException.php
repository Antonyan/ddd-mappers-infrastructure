<?php
namespace Infrastructure\Models\Soap;

use Infrastructure\Exceptions\InternalException;
use Throwable;

class SoapFaultException extends InternalException
{
    private const HTTP_REQUEST_FAILED = 400;

    public function __construct(
        string $message = '',
        int $statusCode = self::HTTP_REQUEST_FAILED,
        int $errorCode = self::DEFAULT_ERROR_CODE,
        array $headers = [],
        array $body = [],
        ?Throwable $previous = null,
        int $code = 0
    ) {
        parent::__construct($message, $errorCode, $headers, $statusCode, $body, $previous, $code);
    }
}