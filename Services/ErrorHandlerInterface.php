<?php
namespace Infrastructure\Services;

use Infrastructure\Exceptions\HttpExceptionInterface;
use Symfony\Component\HttpFoundation\Response;

interface ErrorHandlerInterface
{
    public function handle(HttpExceptionInterface $exception) : Response;
}