<?php
namespace Infrastructure\Models;

class CollectionFactory
{
    public function create(array $objectsParams, \Closure $buildObject) : Collection
    {
        $collection = new Collection();
        foreach ($objectsParams as $objectParams) {
            $collection->push($buildObject($objectParams));
        }

        return $collection;
    }

    public function createWithPaginationFromArray(array $objectsParams, PaginationData $paginationData, \Closure $buildObject) : PaginationCollection
    {
        $paginationCollection = new PaginationCollection($paginationData->totalCount(), $paginationData->limit(), $paginationData->offset());
        $paginationCollection->merge($this->create($objectsParams, $buildObject));

        return $paginationCollection;
    }

    public function createWithPaginationFromCollection(Collection $collection, PaginationData $paginationData) : PaginationCollection
    {
        $paginationCollection = new PaginationCollection($paginationData->totalCount(), $paginationData->limit(), $paginationData->offset());
        $paginationCollection->merge($collection);

        return $paginationCollection;
    }
}