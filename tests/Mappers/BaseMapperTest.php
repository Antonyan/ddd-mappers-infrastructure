<?php

namespace Infrastructure\Tests\Mappers;

use Infrastructure\Mappers\BaseMapper;
use Infrastructure\Models\Collection;
use Infrastructure\Models\PaginationCollection;
use Infrastructure\Tests\ArraySerializableHelperTrait;
use Infrastructure\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\TestCase;

/**
 * Test for BaseMapper class
 * @see BaseMapper
 */
class BaseMapperTest extends TestCase
{
    use ReflectionHelperTrait;
    use ArraySerializableHelperTrait;

    /**
     * @see BaseMapper::buildPaginationCollection()
     */
    public function testBuildCollection()
    {
        $baseMapper = $this->getMockBuilder(BaseMapper::class)
            ->setMethods(['buildObjectOptionalFields'])
            ->getMockForAbstractClass();

        $testData1 = ['property_1' => uniqid(), 'property_2' => uniqid()];
        $testData2 = ['property_1' => uniqid(), 'property_2' => uniqid()];
        $testData3 = ['property_1' => uniqid(), 'property_2' => uniqid()];

        $testData = [$testData1, $testData2, $testData3];

        $baseMapper
            ->expects($this->exactly(count($testData)))
            ->method('buildObjectOptionalFields')
            ->willReturn(
                ...array_map([$this, 'createArraySerializableObject'], $testData)
            );

        $result = $this->callMethod($baseMapper, 'buildCollection', $testData);

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertEquals($this->createArraySerializableCollection($testData), $result);
    }

    /**
     * @see BaseMapper::buildPaginationCollection()
     */
    public function testBuildPaginationCollection()
    {
        $baseMapper = $this->getMockBuilder(BaseMapper::class)
            ->setMethods(['buildCollection'])
            ->getMockForAbstractClass();

        $testData = [[uniqid()], [uniqid()]];

        /** @var Collection $testCollection */
        $testCollection = $this->createArraySerializableCollection($testData);
        $limit = rand(10, 100);
        $offset = rand(10, 100);

        $baseMapper
            ->expects($this->once())
            ->method('buildCollection')
            ->with(
                $this->equalTo($testData)
            )
            ->willReturn($testCollection);

        /** @var PaginationCollection $paginationCollection */
        $paginationCollection = $this
            ->callMethod($baseMapper, 'buildPaginationCollection', $testData, count($testData), $limit, $offset);

        $this->assertInstanceOf(PaginationCollection::class, $paginationCollection);
        $this->assertEquals($limit, $paginationCollection->getLimit());
        $this->assertEquals($offset, $paginationCollection->getOffset());
        $this->assertEquals($paginationCollection->getCollection(), $testCollection->getCollection());
    }

    /**
     * @see BaseMapper::buildObjectOptionalFields()
     */
    public function testBuildObjectOptionalFields()
    {
        $testData = [[uniqid()], [uniqid()]];

        $baseMapper = $this->getMockBuilder(BaseMapper::class)
            ->setMethods(['buildObject'])
            ->getMockForAbstractClass();

        $baseMapper
            ->expects($this->once())
            ->method('buildObject')
            ->with($testData)
            ->willReturn($testData);

        $this->assertEquals($testData, $this->callMethod($baseMapper, 'buildObjectOptionalFields', $testData));
    }
}
