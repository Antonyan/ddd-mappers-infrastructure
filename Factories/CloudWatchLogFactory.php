<?php

namespace Infrastructure\Factories;

use Infrastructure\Models\Logging\CloudWatchProvider;
use Infrastructure\Models\Logging\Logger;
use Psr\Log\LoggerInterface;

class CloudWatchLogFactory implements LogFactory
{
    /**
     * @var string
     */
    private $applicationName;

    /**
     * @var string
     */
    private $environment;

    /**
     * @var CloudWatchProvider
     */
    private $provider;

    /**
     * CloudWatchLogFactory constructor.
     * @param string $applicationName
     * @param string $environment
     * @param CloudWatchProvider $provider
     */
    public function __construct(string $applicationName, string $environment, CloudWatchProvider $provider)
    {
        $this->applicationName = $applicationName;
        $this->environment = $environment;
        $this->provider = $provider;
    }

    /**
     * @param string $channel
     * @return LoggerInterface
     * @throws \Infrastructure\Exceptions\InfrastructureException
     */
    public function create(string $channel) : LoggerInterface
    {
        return new Logger($channel, [$this->provider->handler($this->getGroupName(), $this->getStreamName())]);
    }

    /**
     * @return string
     */
    private function getGroupName(): string
    {
        return 'php-logs';
    }

    /**
     * @return string
     */
    private function getStreamName(): string
    {
        return $this->applicationName . '-' . $this->environment;
    }
}