<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ClientErrorException extends InternalException
{
    /**
     * ValidationException constructor.
     * @param string $message
     * @param int $code
     */
    public function __construct(string $message, $code = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct(json_encode(['error' => $message]), $code);
    }
}