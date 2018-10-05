<?php
namespace Infrastructure\Models;

class PaginationData
{
    private $totalCount;

    private $limit;

    private $offset;

    public function __construct($totalCount, $limit, $offset)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        $this->totalCount = $totalCount;
    }

    public function totalCount()
    {
        return $this->totalCount;
    }

    public function limit()
    {
        return $this->limit;
    }

    public function offset()
    {
        return $this->offset;
    }
}