<?php declare(strict_types=1);

namespace Infrastructure\Services;

use Infrastructure\Models\ArraySerializable;

abstract class ExecutableEventCommand
{
    abstract public function execute(array $eventData): void;

    abstract protected function createEvent(array $eventData): ArraySerializable;
}