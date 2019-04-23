<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\ErrorData;
use Symfony\Component\HttpFoundation\Response;

class ValidationException extends InternalException
{
    /**
     * ValidationException constructor.
     * @param string    $message
     * @param ErrorData $data
     */
    public function __construct(string $message, ErrorData $data = null)
    {
        parent::__construct($message, self::VALIDATION_ERROR_CODE, $data, Response::HTTP_BAD_REQUEST);
    }
}