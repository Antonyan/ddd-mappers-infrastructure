<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\StringMap;
use Symfony\Component\HttpFoundation\Response;

class ClientErrorException extends InternalException
{
    /**
     * ClientErrorException constructor.
     * @param string    $message
     * @param int       $errorCode
     * @param StringMap $data
     * @param int       $statusCode
     */
    public function __construct(
        string    $message,
        int       $errorCode = self::DEFAULT_ERROR_CODE,
        StringMap $data = null,
        int       $statusCode = Response::HTTP_BAD_REQUEST
    ) {
        parent::__construct($message, $errorCode, $data, $statusCode);
    }
}