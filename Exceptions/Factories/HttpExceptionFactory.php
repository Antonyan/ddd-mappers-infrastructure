<?php
namespace Infrastructure\Models;

use Infrastructure\Exceptions\HttpExceptionInterface;
use Infrastructure\Exceptions\InfrastructureException;

class HttpExceptionFactory
{
    public function create(\Exception $exception) : HttpExceptionInterface
    {
        if ($exception instanceof HttpExceptionInterface) {
            return $exception;
        }

        return new InfrastructureException($exception->getMessage(), $exception);
    }
}