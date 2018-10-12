<?php

namespace Infrastructure\Models\SearchCriteria;

use Infrastructure\Exceptions\InfrastructureException;

class OrderBy
{
    /**
     * @var string
     */
    private $order;

    /**
     * @var string
     */
    private $key;

    /**
     * OrderBy constructor.
     * @param string $order
     * @param string $key
     * @throws InfrastructureException
     */
    public function __construct(string $order, string $key)
    {
        $this->setOrder($order);
        $this->key = $key;
    }

    /**
     * @param string $order
     * @throws InfrastructureException
     */
    private function setOrder(string $order): void
    {
        if (!\in_array($order, [SearchCriteria::ORDER_ASCENDING, SearchCriteria::ORDER_DESCENDING], true)){
            throw new InfrastructureException(
                'Order can be either ' . SearchCriteria::ORDER_ASCENDING . ' or ' . SearchCriteria::ORDER_DESCENDING);
        }
        $this->order = $order;
    }

    /**
     * @return array
     */
    public function toOrderCondition() : array
    {
        return [$this->key => $this->order];
    }
}