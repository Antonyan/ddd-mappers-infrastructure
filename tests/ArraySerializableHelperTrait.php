<?php

namespace Infrastructure\Tests;


use Infrastructure\Models\ArraySerializable;
use Infrastructure\Models\Collection;

trait ArraySerializableHelperTrait
{
    /**
     * @param array $data
     * @return Collection
     */
    private function createArraySerializableCollection(array $data): Collection
    {
        $collection = new Collection();

        foreach ($data as $datum) {
            $collection->push($this->createArraySerializableObject($datum));
        }

        return $collection;
    }

    /**
     * @param array $data
     * @return ArraySerializable
     */
    private function createArraySerializableObject(array $data)
    {
        return new class($data) implements ArraySerializable {
            private $data;
            public function __construct(array $data)
            {
                $this->data = $data;
            }

            /**
             * @return array
             */
            public function toArray(): array
            {
                return $this->data;
            }
        };
    }
}