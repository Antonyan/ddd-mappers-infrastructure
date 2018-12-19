<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class ApplicationRegistryException extends InternalException
{
    /**
     * ApplicationRegistryException constructor.
     * @param string $message
     */
    public function __construct($message = 'Such key not found in registry')
    {
        parent::__construct(
            $message,
            Response::HTTP_BAD_REQUEST
        );
    }
}