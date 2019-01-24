<?php

namespace Infrastructure\Models\SearchCriteria;

abstract class SearchCriteria
{
    public const CONDITIONS = 'conditions';
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';
    public const ORDER_BY = 'orderBy';
    public const WHERE_CONDITIONS = 'where';

    public const ORDER_ASCENDING = 'orderByAsc';
    public const ORDER_DESCENDING = 'orderByDesc';

    public const WHERE_IN = 'in';
    public const WHERE_LIKE = 'like';

    public const WHERE_EQUAL = 'eq';
    public const WHERE_NOT_EQUAL = 'ne';
    public const WHERE_GREATER = 'gt';
    public const WHERE_GREATER_OR_EQUAL = 'ge';
    public const WHERE_LESS = 'lt';
    public const WHERE_LESS_OR_EQUAL = 'le';

    public const OR_WHERE_EQUAL = 'orEqual';
    public const OR_WHERE_LIKE = 'orlike';

    public const WHERE_EQUAL_SIGN = '=';
    public const WHERE_NOT_EQUAL_SIGN = '<>';
    public const WHERE_GREATER_SIGN = '>';
    public const WHERE_GREATER_OR_EQUAL_SIGN = '>=';
    public const WHERE_LESS_SIGN = '<';
    public const WHERE_LESS_OR_EQUAL_SIGN = '<=';
    public const WHERE_IS_NULL_SIGN = 'IS NULL';

    public const TYPE_DATE = 'DATE';
    public const TYPE_DECIMAL = 'DECIMAL';

    public const MAX_LIMIT = 1000;
    public const LIMIT_IS_NO_SPECIFIED = 0;

    abstract public function limit(): int;

    abstract public function offset() : int;

    abstract public function orderBy() : array;

    abstract public function groupBy() : array;

    abstract public function conditions() : array;

    abstract public function getType($field) : string;

    abstract public function isSetType($field) : bool;

    public function isSpecifiedLimit(): bool
    {
        return $this->limit() !== self::LIMIT_IS_NO_SPECIFIED;
    }
}