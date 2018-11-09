<?php

namespace Infrastructure\Exceptions;

use Throwable;

class InternalException extends \Exception implements HttpExceptionInterface
{
    /**
     * @var array
     */
    protected $headers;

    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var array
     */
    protected $body;

    /**
     * @var int
     */
    protected $errorCode;

    /**
     * InternalException constructor.
     * @param string $message
     * @param $statusCode
     * @param int $errorCode
     * @param array $headers
     * @param array $body
     * @param Throwable|null $previous
     * @param int $code
     */
    public function __construct($message = '', $statusCode, $errorCode = self::DEFAULT_ERROR_CODE, $headers = [], $body = [],  Throwable $previous = null, $code = 0)
    {
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
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getBody(): array
    {
        return $this->body;
    }

    /**
     * @return int
     */
    public function getErrorCode(): int
    {
        return $this->errorCode;
    }


}