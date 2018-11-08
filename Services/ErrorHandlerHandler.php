<?php
namespace Infrastructure\Services;

use Infrastructure\Exceptions\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

class ErrorHandlerHandler implements ErrorHandlerInterface
{
    public function handle(HttpExceptionInterface $exception): Response
    {
        throw $exception;
    }

}