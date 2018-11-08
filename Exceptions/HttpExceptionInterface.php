<?php
namespace Infrastructure\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface as SymfonyHttpExceptionInterface;

interface HttpExceptionInterface extends SymfonyHttpExceptionInterface
{
    public const DEFAULT_ERROR_CODE = 0;

    public const VALIDATION_ERROR_CODE = 100;

    public function getBody() : array;

    public function getErrorCode() : int;
}