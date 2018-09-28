<?php

namespace Infrastructure\Models\Db;

use Infrastructure\Exceptions\InfrastructureException;

class BatchSave
{
    /**
     * @var string
     */
    private $table;

    /**
     * @var array
     */
    private $parametersList;

    /**
     * @var array
     */
    private $parametersForBind = [];

    /**
     * @var string
     */
    private $queryValues;

    /**
     * @var int
     */
    private $placeholderCounter = 1;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * BatchSave constructor.
     * @param string $table
     * @param array $parametersList
     */
    public function __construct(string $table, array $parametersList)
    {
        $this->table = $table;
        $this->parametersList = $parametersList;
    }

    /**
     * @return string
     * @throws InfrastructureException
     */
    public function query() : string
    {
        return 'INSERT INTO ' . $this->table . ' (' . implode(',', $this->fields()) . ') VALUES '
            . $this->buildValues()->queryValues . ' ON DUPLICATE KEY UPDATE ' . $this->updateConditions();
    }

    /**
     * @return array
     * @throws InfrastructureException
     */
    public function params() : array
    {
        return $this->buildValues()->parametersForBind;
    }

    /**
     * @return string
     */
    private function updateConditions() : string
    {
        $conditions = '';

        foreach ($this->fields() as $filed) {
            $conditions .= $filed . ' = VALUES(' . $filed . '),';
        }

        return trim($conditions, ',');;
    }

    /**
     * @return array
     * @throws InfrastructureException
     */
    private function fields() : array
    {
        if (\count($this->fields)){
            return $this->fields;
        }

        if (! \count($this->parametersList)) {
            throw new InfrastructureException('You should pass at least one entity for batchInsert');
        }

        if (! \count($this->parametersList[0])) {
            throw new InfrastructureException('Empty entity was send for batchInsert');
        }

        $this->fields = array_keys($this->parametersList[0]);

        return $this->fields;
    }

    /**
     * @return BatchSave
     * @throws InfrastructureException
     */
    private function buildValues() : BatchSave
    {
        if ($this->queryValues && $this->parametersForBind){
            return $this;
        }

        foreach ($this->parametersList as $parameters) {
            $this->checkIfEntityHasAllFields($parameters);
            $this->buildConditionsForOneRowInsert($parameters);
        }

        $this->deleteLastCommaFromQueryValues();

        return $this;
    }

    /**
     * @param $parameters
     * @throws InfrastructureException
     */
    private function checkIfEntityHasAllFields($parameters): void
    {
        if (array_diff_key(array_flip($this->fields()), $parameters)) {
            throw new InfrastructureException('You should use entities with the same fields for batchInsert');
        }
    }

    /**
     * @param $parameters
     */
    private function buildValuesForOneRow($parameters): void
    {
        foreach ($this->fields() as $field) {
            $placeholder = ':placeholder' . $this->placeholderCounter++;
            $this->queryValues .= $placeholder . ',';
            $this->parametersForBind[$placeholder] = $parameters[$field];
        }
    }

    /**
     * @param $parameters
     */
    private function buildConditionsForOneRowInsert($parameters): void
    {
        $this->queryValues .= '(';
        $this->buildValuesForOneRow($parameters);
        $this->deleteLastCommaFromQueryValues();
        $this->queryValues .= '),';
    }

    private function deleteLastCommaFromQueryValues(): void
    {
        $this->queryValues = trim($this->queryValues, ',');
    }
}