<?php

namespace Infrastructure\Models\SearchCriteria;

use Infrastructure\Exceptions\InfrastructureException;

abstract class SearchCriteria
{
    public const CONDITIONS = 'conditions';
    public const COMBINING_CONDITIONS_VIA_AND = ' AND ';
    public const COMBINING_CONDITIONS_VIA_OR = ' OR ';
    public const LIMIT = 'limit';
    public const OFFSET = 'offset';
    public const ORDER_BY = 'orderBy';
    public const WHERE_CONDITIONS = 'where';

    public const ORDER_ASCENDING = 'orderByAsc';
    public const ORDER_DESCENDING = 'orderByDesc';

    public const WHERE_IN = 'in';
    public const WHERE_LIKE = 'like';

    public const WHERE_EQUAL = 'eq';
    public const WHERE_GREATER = 'gt';
    public const WHERE_GREATER_OR_EQUAL = 'ge';
    public const WHERE_LESS = 'lt';
    public const WHERE_LESS_OR_EQUAL = 'le';

    public const OR_WHERE_EQUAL = 'orEqual';
    public const OR_WHERE_LIKE = 'orlike';

    public const WHERE_EQUAL_SIGN = '=';
    public const WHERE_GREATER_SIGN = '>';
    public const WHERE_GREATER_OR_EQUAL_SIGN = '>=';
    public const WHERE_LESS_SIGN = '<';
    public const WHERE_LESS_OR_EQUAL_SIGN = '<=';

    public const TYPE_DATE = 'DATE';
    public const TYPE_DECIMAL = 'DECIMAL';

    public const MAX_LIMIT = 100;

    /**
     * @var string
     */
    private $combiningConditions = self::COMBINING_CONDITIONS_VIA_AND;

    abstract public function limit() : int;

    abstract public function offset() : int;

    abstract public function orderBy() : array;

    abstract public function conditions() : array;

    /**
     * @return string
     */
    public function combiningConditions(): string
    {
        return $this->combiningConditions;
    }

    /**
     * @param string $combiningConditions
     * @throws InfrastructureException
     */
    protected function setCombiningConditions(string $combiningConditions): void
    {
        $allowedCombinings = [self::COMBINING_CONDITIONS_VIA_AND, self::COMBINING_CONDITIONS_VIA_OR];
        if (!in_array($combiningConditions, $allowedCombinings)) {
            throw new InfrastructureException(
                'Illegal combining conditions(\'' . $combiningConditions . '\'), allowed: ' . implode(', ', $allowedCombinings)
            );
        }

        $this->combiningConditions = $combiningConditions;
    }
}