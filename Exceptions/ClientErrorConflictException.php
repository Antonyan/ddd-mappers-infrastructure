<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\ErrorData;
use Symfony\Component\HttpFoundation\Response;

class ClientErrorConflictException extends ClientErrorException
{
    /**
     * ClientErrorConflictException constructor.
     * @param string    $message
     * @param int       $errorCode
     * @param ErrorData $data
     */
    public function __construct(string $message, int $errorCode = self::DEFAULT_ERROR_CODE, ErrorData $data = null)
    {
        parent::__construct($message, $errorCode, $data, Response::HTTP_CONFLICT);
    }
}