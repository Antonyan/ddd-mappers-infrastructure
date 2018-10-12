<?php

namespace Infrastructure\Tests\Models\Http;

use Infrastructure\Models\Http\Headers;
use Infrastructure\Models\Http\IllegalHeaderValueException;
use Infrastructure\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\TestCase;

/**
 * Class HeadersTest
 * @see Headers
 */
class HeadersTest extends TestCase
{
    use ReflectionHelperTrait;

    /**
     * @see Headers::hasHeader()
     *
     * @throws \ReflectionException
     */
    public function testHasHeader()
    {
        $data = [
            'X-TEST-1' => ['val_1', 'val_2'],
            'X-TEST-2' => ['val_3', 'val_4'],
            'X-TEST-3' => ['val_5'],
        ];
        /** @var Headers $headers */
        $headers = $this->createInstance(Headers::class);
        $this->setProperty($headers, 'headersContainer', $data);

        $this->assertTrue($headers->hasHeader('X-TEST-1'));
        $this->assertTrue($headers->hasHeader('X-TEST-2'));
        $this->assertTrue($headers->hasHeader('X-TEST-3'));

        $this->assertFalse($headers->hasHeader('X-TEST-6'));
    }

    /**
     * @see Headers::merge()
     *
     * @throws IllegalHeaderValueException
     * @throws \ReflectionException
     */
    public function testMerge()
    {
        $originalHeaders1 = new Headers(['H1' => 'v1', 'H2' => 'v2', 'COMMON' => 'headers_1', 'DIFFERENT_1' => '1']);
        $originalHeaders2 = new Headers(['H3' => 'v3', 'H4' => 'v4', 'COMMON' => 'headers_2', 'DIFFERENT_2' => '2']);

        $headers1 = clone $originalHeaders1;
        $headers2 = clone $originalHeaders2;

        $mergedHeaders = $headers1->merge($headers2);

        $this->assertEquals($originalHeaders1, $headers1);
        $this->assertEquals($originalHeaders2, $headers2);

        $this->assertEquals(
            $this->getProperty($mergedHeaders, 'headersContainer')['COMMON'],
            $this->getProperty($headers2, 'headersContainer')['COMMON']
        );

        $this->assertTrue(
            array_key_exists('DIFFERENT_1', $this->getProperty($mergedHeaders, 'headersContainer'))
        );
        $this->assertTrue(
            array_key_exists('DIFFERENT_2', $this->getProperty($mergedHeaders, 'headersContainer'))
        );
    }

    /**
     * @see Headers::toArray()
     *
     * @throws \ReflectionException
     */
    public function testToArray()
    {
        $data = [
            'X-TEST-1' => ['val_1', 'val_2'],
            'X-TEST-2' => ['val_3', 'val_4'],
            'X-TEST-3' => ['val_5'],
        ];
        /** @var Headers $headers */
        $headers = $this->createInstance(Headers::class);
        $this->setProperty($headers, 'headersContainer', $data);

        $this->assertEquals($data, $headers->toArray());
    }

    /**
     * @see Headers::__construct
     *
     * @throws \Infrastructure\Models\Http\IllegalHeaderValueException
     */
    public function test__construct()
    {
        $testValidHeaders = [
            'Content-Type' => 'application/json',
            'content-length' => 86709,
            'cache-control' => ['private', 'must-revalidate'],
            'Accept-Language' => ['en-US,en', 'q=0.9']
        ];

        $expectedHeaders = [
            'Content-Type' => ['application/json'],
            'content-length' => [86709],
            'cache-control' => ['private', 'must-revalidate'],
            'Accept-Language' => ['en-US,en', 'q=0.9']
        ];

        $headers = new Headers($testValidHeaders);

        $this->assertEquals($expectedHeaders, $headers->toArray());
    }

    /**
     * @see          Headers::__construct
     *
     * @dataProvider invalidHeadersDataProvider
     * @param array $headers
     * @throws IllegalHeaderValueException
     */
    public function test__constructException(array $headers)
    {
        $this->expectException(IllegalHeaderValueException::class);
        new Headers($headers);
    }

    /**
     * @return array
     */
    public function invalidHeadersDataProvider(): array
    {
       return [
           [
               [
                    'Content-Type' => 'application/json',
                    'content-length' => 'test',
                    'cache-control' => ['private', 'must-revalidate'],
                    'Accept-Language' => ['en-US,en', []]
                ]
           ],
           [
              [
                  'Content-Type' => 'application/json',
                  'content-length' => new \stdClass(),
                  'cache-control' => ['private', 'must-revalidate'],
                  'Accept-Language' => ['en-US,en']
              ]
           ]
       ];
    }
}
