<?php

namespace Infrastructure\Models\SearchCriteria;

class GreaterOrEqualCriteria implements Condition
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $value;

    /**
     * GreaterCriteria constructor.
     * @param string $key
     * @param string $value
     */
    public function __construct(string $key, string $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return array
     */
    public function toCondition(): array
    {
        return [$this->key => $this->value];
    }

    /**
     * @return string
     */
    public function type(): string
    {
        return SearchCriteria::WHERE_GREATER_OR_EQUAL_SIGN;
    }
}