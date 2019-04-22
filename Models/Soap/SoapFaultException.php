<?php
namespace Infrastructure\Models\Soap;

use Infrastructure\Exceptions\InternalException;
use Infrastructure\Models\ErrorData;
use Throwable;

class SoapFaultException extends InternalException
{
    private const HTTP_REQUEST_FAILED = 400;

    public function __construct(
        string $message = '',
        int $errorCode = self::DEFAULT_ERROR_CODE,
        ErrorData $body = null,
        int $statusCode = self::HTTP_REQUEST_FAILED,
        ErrorData $headers = null,
        ?Throwable $previous = null,
        int $code = 0
    ) {
        parent::__construct($message, $errorCode, $body, $statusCode, $headers, $previous, $code);
    }
}