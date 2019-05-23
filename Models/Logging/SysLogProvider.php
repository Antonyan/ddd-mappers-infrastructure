<?php

namespace Infrastructure\Models\Logging;

use Infrastructure\Exceptions\InfrastructureException;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\ErrorLogHandler;
use Monolog\Handler\SyslogHandler;

class SysLogProvider
{
    /**
     * @var AbstractProcessingHandler
     */
    private $handler;

    /**
     * @var string
     */
    private $ident;

    public function __construct(string $ident)
    {
        $this->ident = $ident;
    }

    /**
     * @return AbstractProcessingHandler
     * @throws InfrastructureException
     */
    public function handler() : AbstractProcessingHandler
    {
        if ($this->handler !== null) {
            return $this->handler;
        }

        try {
           return $this->handler = (new SyslogHandler($this->ident))
               ->setFormatter(new LineFormatter(null, null, false, true));
        } catch (InvalidArgumentException $exception) {
            throw new InfrastructureException('Can\'t initialize Sys log handler :  ' . $exception->getMessage());
        }
    }
}