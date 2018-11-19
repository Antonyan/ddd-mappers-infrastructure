<?php

namespace Infrastructure\Models\SearchCriteria;

class SearchCriteriaZeroToNullDecorator extends SearchCriteria
{

    /**
     * @var SearchCriteria
     */
    private $criteria;

    /**
     * @var array
     */
    private $fields;

    /**
     * @var null|array
     */
    private $conditions = null;

    /**
     * SearchCriteriaToQueryString constructor.
     * @param SearchCriteria $criteria
     * @param array $fields
     */
    public function __construct(SearchCriteria $criteria, array $fields = [])
    {
        $this->criteria = $criteria;
        $this->fields   = $fields;
    }

    /**
     * @return array
     */
    public function conditions(): array
    {
        if ($this->conditions !== null) {
            return $this->conditions;
        }

        return $this->conditions = $this->decorateZeroCondition($this->getCriteria()->conditions());
    }

    /**
     * @param $conditions
     * @return mixed
     */
    private function decorateZeroCondition(array $conditions)
    {
        $result = $conditions;
        foreach ($conditions as $criteriaSign => $condition) {
            $field = key($condition);
            $value = current($condition);

            if (!in_array($field, $this->fields)) {
                continue;
            }

            if ($value == 0 && $criteriaSign === SearchCriteria::WHERE_EQUAL_SIGN) {
                unset($result[$criteriaSign]);
                $result[SearchCriteria::WHERE_IS_NULL_SIGN] = [$field => null];
            }
        }

        return $result;
    }

    /**
     * @return int
     */
    public function limit(): int
    {
        return $this->getCriteria()->limit();
    }

    /**
     * @return int
     */
    public function offset(): int
    {
        return $this->getCriteria()->offset();
    }

    /**
     * @return array
     */
    public function orderBy(): array
    {
        return $this->getCriteria()->orderBy();
    }

    /**
     * @return array
     */
    public function groupBy() : array
    {
        return $this->getCriteria()->groupBy();
    }

    /**
     * @param $field
     * @return bool
     */
    public function isSetType($field) : bool
    {
        return $this->getCriteria()->isSetType($field);
    }

    /**
     * @param $field
     * @return string
     */
    public function getType($field): string
    {
        return $this->getCriteria()->getType($field);
    }

    /**
     * @return SearchCriteria
     */
    private function getCriteria()
    {
        return $this->criteria;
    }

}