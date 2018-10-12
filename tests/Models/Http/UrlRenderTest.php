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
     */
    public function testRender()
    {
        $urls = ['test_url' => 'http://test.com/:property_1/:property_2'];
        $data = ['property_1' => 'value_1', 'property_2' => 'value_2'];
        $urlRender = new UrlRender($urls);

        /** @var $urlRender UrlRender */
        $this->assertEquals("http://test.com/value_1/value_2", $urlRender->render('test_url', $data));
    }

    /**
     * @see          UrlRender::prepareLoadUrl()
     * @see          UrlRender::prepareGetUrl()
     * @see          UrlRender::prepareCreateUrl()
     * @see          UrlRender::prepareUpdateUrl()
     * @see          UrlRender::prepareDeleteUrl()
     *
     * @dataProvider prepareTestDataProvider
     * @param string $method
     * @param array $urlTemplates
     * @param array $data
     * @param string $expectUrl
     */
    public function testPrepareUrl(string $method, array $urlTemplates, array $data, string $expectUrl)
    {
        $this->assertEquals($expectUrl, call_user_func([(new UrlRender($urlTemplates)), $method], $data));
    }

    /**
     * @return array
     */
    public function prepareTestDataProvider()
    {
        $ulrTemplates = [
            UrlRender::LOAD_URL => 'domain/:property_1',
            UrlRender::GET_URL => 'domain/:property_1',
            UrlRender::CREATE_URL => 'domain/:property_1',
            UrlRender::UPDATE_URL => 'domain/:property_1',
            UrlRender::DELETE_URL => 'domain/:property_1',
        ];

        return [
            ['prepareLoadUrl', $ulrTemplates, ['property_1' => 'load_value'], 'domain/load_value'],
            ['prepareGetUrl', $ulrTemplates, ['property_1' => 'get_value'], 'domain/get_value'],
            ['prepareCreateUrl', $ulrTemplates, ['property_1' => 'create_value'], 'domain/create_value'],
            ['prepareUpdateUrl', $ulrTemplates, ['property_1' => 'update_value'], 'domain/update_value'],
            ['prepareDeleteUrl', $ulrTemplates, ['property_1' => 'delete_value'], 'domain/delete_value'],
        ];
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
