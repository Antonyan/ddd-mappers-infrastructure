<?php

namespace Infrastructure\Models\Http;


use Psr\Http\Message\StreamInterface;

class Response
{
    /**
     * @var \GuzzleHttp\Psr7\Response
     */
    private $guzzleResponse;

    /**
     * Response constructor.
     * @param int $status
     * @param array $headers
     * @param null $body
     * @param string $version
     * @param null $reason
     */
    public function __construct(int $status, array $headers = [], $body = null, $version = '1.1', $reason = null)
    {
        $this->guzzleResponse = new \GuzzleHttp\Psr7\Response(
            $status,
            $headers,
            $body,
            $version,
            $reason
        );
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->guzzleResponse->getStatusCode();
    }

    /**
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->guzzleResponse->getHeaders();
    }

    /**
     * @return StreamInterface
     */
    public function getBody(): ?StreamInterface
    {
        return $this->guzzleResponse->getBody();
    }

    /**
     * @param string $header
     * @return array
     */
    public function getHeader(string $header): array
    {
        return $this->guzzleResponse->getHeader($header);
    }

    /**
     * @param string $header
     * @return string
     */

    public function getHeaderLine(string $header): string
    {
        return $this->guzzleResponse->getHeaderLine($header);
    }

    /**
     * @return string
     */
    public function getReasonPhrase(): ?string
    {
        return $this->guzzleResponse->getReasonPhrase();
    }

    /**
     * @return string
     */
    public function getProtocolVersion(): string
    {
        return $this->guzzleResponse->getProtocolVersion();
    }
}