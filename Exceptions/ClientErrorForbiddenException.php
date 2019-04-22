<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\StringMap;
use Symfony\Component\HttpFoundation\Response;

class ClientErrorForbiddenException extends ClientErrorException
{
    /**
     * ClientErrorForbiddenException constructor.
     * @param string    $message
     * @param int       $errorCode
     * @param StringMap $data
     */
    public function __construct(string $message, int $errorCode = self::DEFAULT_ERROR_CODE, StringMap $data = null)
    {
        parent::__construct($message, $errorCode, $data, Response::HTTP_FORBIDDEN);
    }
}