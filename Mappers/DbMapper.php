<?php

namespace Infrastructure\Mappers;

use Infrastructure\Exceptions\InfrastructureException;
use Infrastructure\Exceptions\QueryBuilderEmptyInQueryException;
use Infrastructure\Exceptions\ResourceNotFoundException;
use Infrastructure\Models\ArraySerializable;
use Infrastructure\Models\Collection;
use Infrastructure\Models\EntityToDataSourceTranslator;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Models\SearchCriteria\EqualCriteria;
use Infrastructure\Models\SearchCriteria\SearchCriteria;
use Infrastructure\Models\SearchCriteria\SearchCriteriaConstructor;
use Infrastructure\Models\SearchCriteria\SearchCriteriaQueryString;
use Infrastructure\Services\BaseFactory;
use Infrastructure\Services\FilterToQueryTranslator;
use Infrastructure\Services\MySQLClient;

abstract class DbMapper extends BaseMapper
{
    /**
     * Signs for where conditions.
     */
    public const EQUAL_SIGN = '=';
    public const GREATER_SIGN = '>';
    public const LESS_SIGN = '<';
    public const GREATER_OR_EQUAL_SIGN = '>=';
    public const LESS_OR_EQUAL_SIGN = '<=';
    public const IN_SIGN = 'in';
    public const LIKE_SIGN = 'like';

    public const SELECT_LIMIT_ALL = 'selectLimitAll';

    public const TABLE = 'table';
    public const COLUMNS = 'columns';
    public const CREATE_CONDITION = 'create';
    public const UPDATE_CONDITION = 'update';

    /**
     * @var BaseFactory
     */
    private $factory;

    /**
     * @var MySQLClient
     */
    protected $mySqlClient;

    /**
     * @var EntityToDataSourceTranslator
     */
    protected $entityToDataSourceTranslator;

    /**
     * DbMapper constructor.
     * @param BaseFactory $factory
     * @param EntityToDataSourceTranslator $entityToDataSourceTranslator
     * @param MySQLClient $mySqlClient
     */
    public function __construct(
        BaseFactory $factory,
        EntityToDataSourceTranslator $entityToDataSourceTranslator,
        MySQLClient $mySqlClient
    ) {
        $this->factory = $factory;
        $this->mySqlClient = $mySqlClient;
        $this->entityToDataSourceTranslator = $entityToDataSourceTranslator;
    }

    /**
     * @param SearchCriteria $filter
     * @return PaginationCollection
     * @throws InfrastructureException
     */
    public function load(SearchCriteria $filter) : PaginationCollection
    {
        /** @var SearchCriteriaQueryString $filter */
        $queryBuilder = new FilterToQueryTranslator($this->entityToDataSourceTranslator->propertyToColumnMap());
        try {
            $whereQueryPart = $queryBuilder->generateWhere($filter);
        } catch (QueryBuilderEmptyInQueryException $exception) {
            return new PaginationCollection(0, $filter->limit(), $filter->offset());
        }

        $query =
            $this->getSelectQuery().' '.
            $whereQueryPart->getQuery().' '.
            $queryBuilder->generateOrderBy($filter).' '.
            $queryBuilder->generateGroupBy($filter).' '.
            $queryBuilder->generateLimit($filter);

        return $this->buildPaginationCollection(
            $this->mySqlClient->fetchAll($query, $whereQueryPart->getBindingValues()),
            $this->getLoadTotalCount(),
            $filter->limit(),
            $filter->offset()
        );
    }

    /**
     * @param array $objectData
     * @return ArraySerializable|mixed
     * @throws InfrastructureException
     */
    public function create(array $objectData)
    {
        return $this->createObject($this->buildObject($objectData));
    }

    /**
     * @param array $objectData
     * @return ArraySerializable|mixed
     * @throws InfrastructureException
     */
    public function update(array $objectData)
    {
        return $this->updateObject($this->buildObject($objectData));
    }

    /**
     * @return array
     */
    protected function getAsColumns() : array
    {
        $asColumns = [];
        foreach ($this->entityToDataSourceTranslator->propertyToColumnMap() as $modelField => $dbColumn) {
            $asColumns[] = $modelField === $dbColumn ? $dbColumn : $dbColumn.' as `'.$modelField.'`';
        }

        return $asColumns;
    }

    /**
     * @param array $objectData
     * @return ArraySerializable
     */
    protected function buildObject(array $objectData) : ArraySerializable
    {
        return $this->getFactory()->create($objectData);
    }

    /**
     * @param ArraySerializable $object
     * @return ArraySerializable
     * @throws InfrastructureException
     */
    protected function createObject(ArraySerializable $object) : ArraySerializable
    {
        $data = $object->toArray();
        return $this->buildObject(array_merge($data,
            [$this->entityToDataSourceTranslator->insertIdentifier() => $this->mySqlClient->insert(
                $this->entityToDataSourceTranslator->table(),
                $this->entityToDataSourceTranslator->translatePropertyInColumn($data))]));
    }

    /**
     * @param Collection $paramsList
     * @throws InfrastructureException
     */
    protected function batchSave(Collection $paramsList) : void
    {
        $parametersForCreate = $paramsList->toArray()[Collection::ITEMS];
        $parametersForInsert = [];

        foreach ($parametersForCreate as $entityParams) {
            $parametersForInsert[] = $this->entityToDataSourceTranslator->translatePropertyInColumn($entityParams);
        }

        $this->mySqlClient->batchSave(
            $this->entityToDataSourceTranslator->table(),
            $parametersForInsert
        );
    }

    /**
     * @param ArraySerializable $object
     * @return ArraySerializable
     * @throws InfrastructureException
     */
    protected function updateObject(ArraySerializable $object) : ArraySerializable
    {
        $this->mySqlClient->update(
            $this->entityToDataSourceTranslator->table(),
            $this->entityToDataSourceTranslator->extractUpdateParams($object->toArray()),
            $this->entityToDataSourceTranslator->extractUpdateIdentifiers($object->toArray())
        );

        return $object;
    }

    /**
     * @param array $identifiers
     * @return ArraySerializable
     * @throws InfrastructureException
     * @throws ResourceNotFoundException
     */
    public function get(array $identifiers) : ArraySerializable
    {
        $conditions = [];
        foreach ($identifiers as $indName => $indValue) {
            $conditions[] = new EqualCriteria($indName, $indValue);
        }

        $entities = $this->load(new SearchCriteriaConstructor($conditions, 1));

        if ($entities->count() == 0) {
            throw new ResourceNotFoundException("Resource not found(" . $this->currentTable()  . ")!");
        }

        return $entities->getFirst();
    }

    /**
     * @return string
     */
    protected function getSelectQuery() : string
    {
        return 'SELECT SQL_CALC_FOUND_ROWS '.implode(', ', $this->getAsColumns()).' FROM '.
               $this->entityToDataSourceTranslator->table() .' '. $this->getJoins().' ';
    }

    /**
     * @return string
     */
    protected function getJoins() : string
    {
        return '';
    }

    /**
     * @return string
     */
    protected function currentTable() : string
    {
        return $this->entityToDataSourceTranslator->table();
    }

    /**
     * @return int
     * @throws InfrastructureException
     */
    protected function getLoadTotalCount() : int
    {
        return $this->mySqlClient->fetch('SELECT FOUND_ROWS() as count', [])['count'];
    }

    /**
     * @param string $byPropertyName
     * @param $propertyValue
     * @return bool
     * @throws InfrastructureException
     */
    public function delete(string $byPropertyName, $propertyValue) : bool
    {
        $this->mySqlClient->delete($this->entityToDataSourceTranslator->table(), [$byPropertyName => $propertyValue]);
        return true;
    }

    /**
     * @param array $keyValue
     * @return bool
     * @throws InfrastructureException
     */
    public function deleteBySeveralKeys(array $keyValue) : bool
    {
        $this->mySqlClient->delete($this->entityToDataSourceTranslator->table(), $keyValue);
        return true;
    }

    /**
     * @param SearchCriteria $filter
     * @throws InfrastructureException
     */
    public function batchDelete(SearchCriteria $filter) : void
    {
        /** @var SearchCriteriaQueryString $filter */
        $queryBuilder = new FilterToQueryTranslator($this->entityToDataSourceTranslator->propertyToColumnMap());
        try {
            $whereQueryPart = $queryBuilder->generateWhere($filter);
        } catch (QueryBuilderEmptyInQueryException $exception) {
            return;
        }

        $query = 'DELETE FROM ' . $this->entityToDataSourceTranslator->table() . $whereQueryPart->getQuery();

        $this->mySqlClient->execute($query, $whereQueryPart->getBindingValues());
    }

    /**
     * @return string
     */
    protected function getClassName() : string
    {
        return \get_class($this);
    }

    /**
     * @return BaseFactory
     */
    protected function getFactory() : BaseFactory
    {
        return $this->factory;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function getColumnValue(array $data): array
    {
        $filteredFields = array_intersect_key($data, $this->entityToDataSourceTranslator->propertyToColumnMap());

        $columnValue = [];
        foreach ($filteredFields as $key => $value) {
            $columnValue[$this->entityToDataSourceTranslator->propertyToColumnMap()[$key]] = $value;
        }

        return $columnValue;
    }
}
