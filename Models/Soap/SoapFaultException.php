<?php
namespace Infrastructure\Models\Soap;

use Infrastructure\Exceptions\InternalException;
use Throwable;

class SoapFaultException extends InternalException
{
    private const HTTP_REQUEST_FAILED = 400;

    public function __construct(string $message = '', int $statusCode = self::HTTP_REQUEST_FAILED , array $headers = [], ?Throwable $previous = null, int $code = 0)
    {
        parent::__construct($message, $statusCode, $headers, $previous, $code);
    }
}