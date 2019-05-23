<?php

namespace Infrastructure\Models\Logging;

use Infrastructure\Exceptions\InfrastructureException;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\ErrorLogHandler;

class ErrorLogProvider
{
    /**
     * @var AbstractProcessingHandler
     */
    private $handler;

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
           return $this->handler = (new ErrorLogHandler())
               ->setFormatter(new LineFormatter(null, null, false, true));
        } catch (InvalidArgumentException $exception) {
            throw new InfrastructureException('Can\'t initialize Error log handler :  ' . $exception->getMessage());
        }
    }
}