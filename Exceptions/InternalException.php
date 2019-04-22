<?php

namespace Infrastructure\Exceptions;

use Infrastructure\Models\StringMap;
use Throwable;

class InternalException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var StringMap
     */
    protected $headers;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var StringMap
     */
    protected $body;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * InternalException constructor.
     * @param string         $message
     * @param int            $errorCode
     * @param StringMap|null $body
     * @param int            $statusCode
     * @param StringMap|null $headers
     * @param Throwable|null $previous
     * @param int            $code
     */
    public function __construct(
        string $message = '',
        int $errorCode = self::DEFAULT_ERROR_CODE,
        ?StringMap $body = null,
        int $statusCode = 500,
        ?StringMap $headers = null,
        Throwable $previous = null,
        int $code = 0
    ) {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->body = $body;
        $this->errorCode = $errorCode;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers === null ? [] : $this->headers->toArray();
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body === null ? [] : $this->body->toArray();
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }
}