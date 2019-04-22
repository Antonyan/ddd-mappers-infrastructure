<?php
namespace Infrastructure\Exceptions;

use Infrastructure\Models\StringMap;
use Symfony\Component\HttpFoundation\Response;

class ApplicationRegistryException extends InternalException
{
    /**
     * ApplicationRegistryException constructor.
     * @param string $message
     */
    public function __construct(string $message = 'Such key not found in registry')
    {
        parent::__construct($message, self::DEFAULT_ERROR_CODE, new StringMap(), Response::HTTP_BAD_REQUEST);
    }
}