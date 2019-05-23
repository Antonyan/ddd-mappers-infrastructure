<?php

namespace Infrastructure\Models\Logging;

use Infrastructure\Exceptions\InfrastructureException;
use InvalidArgumentException;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class StreamProvider
{
    /**
     * @param $fileName
     * @return AbstractProcessingHandler
     * @throws \Exception
     */
    public function handler($fileName): AbstractProcessingHandler
    {
        try {
            return (new StreamHandler($fileName, Logger::INFO))
                ->setFormatter(new LineFormatter(null, null, false, true));
        } catch (InvalidArgumentException $exception) {
            throw new InfrastructureException('Can\'t initialize CloudWatch ' . $exception->getMessage());
        }
    }
}