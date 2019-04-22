<?php

namespace Infrastructure\Exceptions;

use Infrastructure\Models\ErrorData;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class InfrastructureException extends InternalException
{
    /**
     * InfrastructureException constructor.
     * @param string $message
     * @param null|Throwable $previous
     */
    public function __construct(string $message, Throwable $previous = null)
    {
        parent::__construct($message, self::DEFAULT_ERROR_CODE, new ErrorData(), Response::HTTP_INTERNAL_SERVER_ERROR, new ErrorData(), $previous);
    }
}