<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ClientErrorException extends InternalException
{
    /**
     * ValidationException constructor.
     * @param string $message
     */
    public function __construct(string $message)
    {
        parent::__construct(json_encode(['error' => $message]), Response::HTTP_BAD_REQUEST);
    }
}