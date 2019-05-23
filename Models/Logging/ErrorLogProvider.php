<?php

namespace Infrastructure\Models\Logging;

use Infrastructure\Exceptions\InfrastructureException;
use InvalidArgumentException;
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
           return $this->handler = new ErrorLogHandler();
        } catch (InvalidArgumentException $exception) {
            throw new InfrastructureException('Can\'t initialize Error log handler :  ' . $exception->getMessage());
        }
    }
}