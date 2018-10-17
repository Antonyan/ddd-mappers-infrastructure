<?php

namespace Infrastructure\Exceptions;


use Symfony\Component\HttpFoundation\Response;

class ClientErrorConflictException extends ClientErrorException
{
    /**
     * ClientErrorConflictException constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct($message, Response::HTTP_CONFLICT);
    }
}