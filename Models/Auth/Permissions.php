<?php

namespace Infrastructure\Models\Auth;

use Infrastructure\Exceptions\ClientErrorForbiddenException;
use Infrastructure\Services\BaseService;

class Permissions
{
    /**
     * @var array
     */
    private $permissions;

    /**
     * Permissions constructor.
     * @param array $permissions
     */
    public function __construct(array $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * @param BaseService $controller
     * @param string $method
     * @return array
     * @throws ClientErrorForbiddenException
     */
    public function policies(BaseService $controller, string $method) : array
    {
        $controllerName = \get_class($controller);

        foreach ($this->validationMap() as $check) {
            if ($check($controllerName, $method) === true){
                throw new ClientErrorForbiddenException('Access denied');
            }
        }

        return $this->permissions[$controllerName][$method];
    }

    /**
     * @return array
     */
    private function validationMap(): array
    {
        return [
            function ($controllerName, $method) {
                return $this->thereAreNoRulesForService($controllerName, $method);
            },
            function ($controllerName, $method) {
                return $this->thereAreNoRulesForMethod($controllerName, $method);
            },
        ];
    }

    /**
     * @param $requestedPresentationService
     * @return bool
     */
    private function thereAreNoRulesForService($requestedPresentationService): bool
    {
        return !array_key_exists($requestedPresentationService, $this->permissions);
    }

    /**
     * @param $requestedPresentationService
     * @param $requestedMethod
     * @return bool
     */
    private function thereAreNoRulesForMethod($requestedPresentationService, $requestedMethod): bool
    {
        return !array_key_exists($requestedMethod,
            $this->permissions[$requestedPresentationService]);
    }
}