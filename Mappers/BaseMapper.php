<?php

namespace Infrastructure\Mappers;

use Infrastructure\Models\ArraySerializable;
use Infrastructure\Models\Collection;
use Infrastructure\Models\CollectionFactory;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Models\PaginationData;
use Infrastructure\Models\SearchCriteria\SearchCriteria;

abstract class BaseMapper
{
    /**
     * @param array $objectData
     * @return mixed
     */
    abstract protected function buildObject(array $objectData);

    /**
     * @param array $objectData
     * @return mixed
     */
    abstract public function create(array $objectData);

    /**
     * @param array $objectData
     * @return mixed
     */
    abstract public function update(array $objectData);

    /**
     * @param SearchCriteria $filter
     * @return PaginationCollection
     */
    abstract public function load(SearchCriteria $filter) : PaginationCollection;

    /**
     * @param string $byPropertyName
     * @param $propertyValue
     * @return bool
     */
    abstract public function delete(string $byPropertyName, $propertyValue) : bool;

    /**
     * @param ArraySerializable $object
     * @return ArraySerializable
     */
    abstract protected function createObject(ArraySerializable $object) : ArraySerializable;

    /**
     * @param ArraySerializable $object
     * @return ArraySerializable
     */
    abstract protected function updateObject(ArraySerializable $object) : ArraySerializable;

    /**
     * @param array $objectsParams
     * @param $totalCount
     * @param $limit
     * @param $offset
     * @return PaginationCollection
     */
    protected function buildPaginationCollection(array $objectsParams, $totalCount, $limit, $offset) : PaginationCollection
    {
        return (new CollectionFactory())
                    ->createWithPaginationFromArray(
                        $objectsParams,
                        new PaginationData($totalCount, $limit, $offset),
                        function (array $objectParam) {
                            return $this->buildObject($objectParam);
                        }
                    );
    }

    /**
     * @param array $objectsParams
     * @return Collection
     */
    protected function buildCollection(array $objectsParams) : Collection
    {
        return (new CollectionFactory())->create(
                    $objectsParams,
                    function (array $objectParam) {
                        return $this->buildObject($objectParam);
                    }
                );
    }
}
