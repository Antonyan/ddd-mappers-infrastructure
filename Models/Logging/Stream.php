<?php

namespace Infrastructure\Models\Logging;

use Infrastructure\Exceptions\InfrastructureException;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class Stream
{
    /**
     * @var AbstractProcessingHandler
     */
    private $handler;

    /**
     * @param $fileName
     * @return AbstractProcessingHandler
     * @throws \Exception
     */
    public function handler($fileName): AbstractProcessingHandler
    {
        if ($this->handler !== null) {
            return $this->handler;
        }

        try {
            $handler = new StreamHandler($fileName, Logger::INFO);
        } catch (InvalidArgumentException $exception) {
            throw new InfrastructureException('Can\'t initialize CloudWatch ' . $exception->getMessage());
        }

        $this->handler = $handler->setFormatter(new LineFormatter(null, null, false, true));

        return $this->handler;
    }
}