<?php

namespace Infrastructure\Tests\Models\SearchCriteria;

use Infrastructure\Models\SearchCriteria\SearchCriteriaQueryString;
use PHPUnit\Framework\TestCase;

class SearchCriteriaQueryStringTest extends TestCase
{
    public function testConditionCreations()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['id' => 1]))->conditions(), ['=' => ['id' => 1]]);
    }
    public function testConditionInArrayCreation()
    {
        $this->assertEquals((new SearchCriteriaQueryString(['id' => '1,2']))->conditions(), ['in' => ['id' => [1,2]]]);
    }
    public function testSimpleAndInArrayConditionCombine()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['id' => '1,2', 'name' => 'John']))->conditions(), ['in' => ['id' => [1,2]], '=' => ['name' => 'John']]);
    }
    public function testOrderByASC()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['eq' => ['orderByASC' => 'name']]))->orderBy(), ['=' => ['name' => 'asc']]);
    }
    public function testOrderByDESC()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['eq' => ['orderByDESC' => 'name']]))->orderBy(), ['=' => ['name' => 'desc']]);
    }
    public function testLimit()
    {
        $this->assertEquals((new SearchCriteriaQueryString(['eq' => ['limit' => 1]]))->limit(), 1);
    }
    public function testOffset()
    {
        $this->assertEquals((new SearchCriteriaQueryString(['eq' => ['offset' => 1]]))->offset(), 1);
    }
    public function testLike()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['like' => ['id' => 5]]))->conditions(), ['like' => ['id' => '%5%']]);
    }
    public function testGreaterThen()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['gt' => ['id' => 5]]))->conditions(), ['>' => ['id' => '5']]);
    }
    public function testLessThen()
    {
        $this->assertArraySubset((new SearchCriteriaQueryString(['lt' => ['id' => 5]]))->conditions(), ['<' => ['id' => '5']]);
    }
}