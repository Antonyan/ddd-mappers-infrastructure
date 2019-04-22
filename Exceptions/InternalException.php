<?php

namespace Infrastructure\Exceptions;

use Infrastructure\Models\ErrorData;
use Throwable;

class InternalException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var ErrorData
     */
    protected $headers;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var ErrorData
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
     * @param ErrorData|null $body
     * @param int            $statusCode
     * @param ErrorData|null $headers
     * @param Throwable|null $previous
     * @param int            $code
     */
    public function __construct(
        string $message = '',
        int $errorCode = self::DEFAULT_ERROR_CODE,
        ?ErrorData $body = null,
        int $statusCode = 500,
        ?ErrorData $headers = null,
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