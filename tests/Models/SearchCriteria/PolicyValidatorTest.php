<?php

namespace Infrastructure\tests\Models\SearchCriteria;

use Exception;
use Infrastructure\Exceptions\ClientErrorException;
use Infrastructure\Models\Auth\Permissions;
use Infrastructure\Models\Auth\RoleExtractor;
use Infrastructure\Models\Auth\Rule;
use Infrastructure\Models\ContainerBuilder;
use Infrastructure\Services\Auth\Policy;
use Infrastructure\Services\Auth\PolicyValidator;
use Infrastructure\Services\BaseService;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class PolicyValidatorTest extends TestCase
{
    public function testValidate()
    {
        $permissions = [
            OnePresentationService::class => [
                'load' => [new Rule('cm', [PolicyOne::class])],
            ],
        ];

        $policies = new ContainerBuilder();
        $policies->register(PolicyOne::class, PolicyOne::class);

        $validator = new PolicyValidator(new Permissions($permissions), $policies, new CustomRoleExtractor());

        try {
            $validator->validate(new Request(), new OnePresentationService(), 'load');
        } catch (Exception $exception) {
            $this->assertTrue(false);
        }

        $this->assertTrue(true);
    }

    public function testValidateAccessDenied()
    {
        $permissions = [
            OnePresentationService::class => [
                'load' => [new Rule('cm', [PolicyTwo::class])],
            ],
        ];

        $policies = new ContainerBuilder();
        $policies->register(PolicyTwo::class, PolicyTwo::class);

        $validator = new PolicyValidator(new Permissions($permissions), $policies, new CustomRoleExtractor());

        $this->expectException(ClientErrorException::class);
        $this->expectExceptionMessage('Access denied. Please, check policy and role');

        $validator->validate(new Request(), new OnePresentationService(), 'load');
    }
}

class PolicyOne implements Policy
{
    public function check(Request $request): bool
    {
        return true;
    }
}

class PolicyTwo implements Policy
{
    public function check(Request $request): bool
    {
        return false;
    }
}

class CustomRoleExtractor implements RoleExtractor
{
    public function userRoles(Request $request): array
    {
        return ['cm'];
    }
}


class OnePresentationService extends BaseService
{

}