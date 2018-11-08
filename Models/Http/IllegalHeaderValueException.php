<?php

namespace Infrastructure\Models\Http;


use Infrastructure\Exceptions\InfrastructureException;

class IllegalHeaderValueException extends InfrastructureException
{
    public function __construct($value)
    {
        parent::__construct('Illegal type value: ' . (is_object($value) ? get_class($value) : gettype($value)));
    }
}