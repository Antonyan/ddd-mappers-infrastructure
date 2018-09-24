<?php

namespace Infrastructure\Tests\Models\Http;

use Infrastructure\Models\Http\UrlRender;
use Infrastructure\Tests\ReflectionHelperTrait;
use PHPUnit\Framework\TestCase;

/**
 * Test for UrlRender
 * @see UrlRender
 */
class UrlRenderTest extends TestCase
{
    use ReflectionHelperTrait;

    /**
     * @see UrlRender::__construct()
     * @throws \ReflectionException
     */
    public function test__construct()
    {
        $urlConfig = [
            'url1' => uniqid() . '-url1.com',
            'url2' => uniqid() . '-url2.com',
            'url3' => uniqid() . '-url3.com',
        ];

        $urlRender = new UrlRender($urlConfig);

        $this->assertEquals($urlConfig, $this->getProperty($urlRender, 'urlsPlaceholders'));
    }

    /**
     * @see UrlRender::extractPlaceholders()
     * @throws \ReflectionException
     */
    public function testExtractPlaceholders()
    {
        $data = [
            'property_1' => 'value1',
            'property_2' => 'value2',
            'property_3' => 'value3',
            'property_4' => 'value4',
        ];

        $placeholders = [
            ':property_1', ':property_2', ':property_3', ':property_4'
        ];

        $this->assertEquals($placeholders, $this->callMethod(new UrlRender([]), 'extractPlaceholders' ,$data));
    }

    /**
     * @see          UrlRender::buildUrl()
     * @dataProvider paramsUrlDataProvider
     * @param array $params
     * @param string $url
     * @param string $buildUrl
     * @throws \ReflectionException
     */
    public function testBuildUrl(array $params, string $url, string $buildUrl)
    {
        $this->assertEquals($buildUrl, $this->callMethod(new UrlRender([]), 'buildUrl', $url, $params));
    }

    /**
     * @see UrlRender::render()
     * @throws \ReflectionException
     */
    public function testRender()
    {
        $urlRender = $this->getMockBuilder(UrlRender::class)
            ->disableOriginalConstructor()
            ->setMethods(['extractPlaceholders'])
            ->getMock();

        $urls = ['test_url' => 'http://test.com/:property_1/:property_2'];
        $data = ['property_1' => 'value_1', 'property_2' => 'value_2'];

        $urlRender
            ->expects($this->once())
            ->method('extractPlaceholders')
            ->with($data)
            ->willReturn([':property_1', ':property_2']);

        $this->setPrivatePropertyInMock($urlRender, 'urlsPlaceholders', $urls);


        /** @var $urlRender UrlRender */
        $this->assertEquals("http://test.com/value_1/value_2", $urlRender->render('test_url', $data));
    }

    public function testPrepareDeleteUrl()
    {

    }

    public function testPrepareGetUrl()
    {

    }

    public function testPrepareLoadUrl()
    {

    }

    public function testPrepareUpdateUrl()
    {

    }

    public function testPrepareCreateUrl()
    {

    }


    /**
     * @return array
     */
    public function paramsUrlDataProvider()
    {
        return [
            [
                [], 'http://test.com', 'http://test.com'
            ],
            [
                ['property_1' => 'value_1', 'property_2' => 'value_2'],
                'http://test.com',
                'http://test.com?property_1=value_1&property_2=value_2'
            ],
            [
                ['property_3' => 'value_3', 'property_4' => 'value_4'],
                'http://test.com?test_property=test_value',
                'http://test.com?test_property=test_value&property_3=value_3&property_4=value_4'
            ],
            [
                ['property_5' => 'value_5', 'property_6' => 'value_6'],
                'http://test.com?test_property=test_value&',
                'http://test.com?test_property=test_value&property_5=value_5&property_6=value_6'
            ],
            [
                ['property_7' => 'value_7', 'property_8' => 'value_8'],
                'http://test.com?',
                'http://test.com?property_7=value_7&property_8=value_8'
            ],
        ];
    }
}
