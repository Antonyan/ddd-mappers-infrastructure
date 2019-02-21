<?php

namespace Infrastructure\Tests\Models\SearchCriteria;

use Infrastructure\Exceptions\ClientErrorException;
use Infrastructure\Models\Auth\Permissions;
use Infrastructure\Models\Auth\Rule;
use Infrastructure\Services\BaseService;
use PHPUnit\Framework\TestCase;

class PermissionsTest extends TestCase
{
    /**
     * @throws ClientErrorException
     */
    public function testGetPolicies() : void
    {
        $policies = [
            SomePresentationService::class => [
                'load' => [new Rule('cm', ['PolicyOne', 'PolicyTwo'])],
            ],
        ];

        $rule = (new Permissions($policies))->policies(new SomePresentationService(), 'load')[0];
        $this->assertArraySubset($rule->policies(), ['PolicyOne', 'PolicyTwo']);
    }

    /**
     * @throws ClientErrorException
     */
    public function testGetDefaultPolicies() : void
    {
        $policies = [
            SomePresentationService::class => [
                'create' => [new Rule('cm')],
            ],
        ];

        $rule = (new Permissions($policies))->policies(new SomePresentationService(), 'create')[0];
        $this->assertArraySubset($rule->policies(), []);
    }

}

class SomePresentationService extends BaseService
{

}