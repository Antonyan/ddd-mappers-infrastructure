<?php

namespace Infrastructure\Services\Auth;

use Infrastructure\Exceptions\ClientErrorException;
use Infrastructure\Exceptions\InternalException;
use Infrastructure\Models\Auth\Permissions;
use Infrastructure\Models\Auth\RoleExtractor;
use Infrastructure\Models\Auth\Rule;
use Infrastructure\Models\ContainerBuilder;
use Infrastructure\Services\BaseService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PolicyValidator
{
    /**
     * @var Permissions
     */
    private $permissions;

    /**
     * @var ContainerBuilder
     */
    private $policies;

    /**
     * @var RoleExtractor
     */
    private $roleExtractor;

    /**
     * PolicyValidator constructor.
     * @param Permissions $permissions
     * @param ContainerBuilder $policies
     * @param RoleExtractor $roleExtractor
     */
    public function __construct(Permissions $permissions, ContainerBuilder $policies, RoleExtractor $roleExtractor)
    {
        $this->permissions = $permissions;
        $this->policies = $policies;
        $this->roleExtractor = $roleExtractor;
    }

    /**
     * @param Request $request
     * @param BaseService $controller
     * @param string $method
     * @throws ClientErrorException
     * @throws InternalException
     */
    public function validate(Request $request, BaseService $controller, string $method) : void
    {
        /** @var Rule $rule */
        foreach ($this->permissions->policies($controller, $method) as $rule) {
            if (
                $this->isRoleHasAccess($request, $rule)
                &&
                $this->allPoliciesMet($rule->policies(), $request)
            ){
                return;
            }
        }

        throw new ClientErrorException('Access denied. Please, check policy and role', 403);
    }

    /**
     * @param array $policies
     * @param Request $request
     * @return bool
     * @throws InternalException
     */
    private function allPoliciesMet(array $policies, Request $request) : bool
    {
        /** @var Policy $policy */
        foreach ($policies as $policy) {

            if (!$this->policies->has($policy)){
                throw new InternalException('Can\'t find policy ' . $policy . '.', Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            if ($this->policies->get($policy)->check($request) === false) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Request $request
     * @param Rule $rule
     * @return bool
     */
    private function isRoleHasAccess(Request $request, Rule $rule): bool
    {
        return \in_array($rule->roleName(), $this->roleExtractor->userRoles($request), true);
    }
}