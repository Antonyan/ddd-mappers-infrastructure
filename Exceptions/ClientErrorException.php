<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\ErrorData;
use Symfony\Component\HttpFoundation\Response;

class ClientErrorException extends InternalException
{
    /**
     * ClientErrorException constructor.
     * @param string    $message
     * @param int       $errorCode
     * @param ErrorData $data
     * @param int       $statusCode
     */
    public function __construct(
        string    $message,
        int       $errorCode = self::DEFAULT_ERROR_CODE,
        ErrorData $data = null,
        int       $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $errorCode, $data, $statusCode);
    }
}