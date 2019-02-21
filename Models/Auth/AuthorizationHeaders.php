<?php
namespace Infrastructure\Models\Auth;

use Infrastructure\Exceptions\ApplicationRegistryException;
use Infrastructure\Models\ApplicationRegistry;
use Infrastructure\Models\ArraySerializable;

class AuthorizationHeaders implements ArraySerializable
{
    public const REGISTRY_KEY_AUTH_HEADER_ROLES = 'authHeaderRoles';
    public const REGISTRY_KEY_AUTH_HEADER_USER_ID = 'authHeaderUserId';

    public const AUTH_HEADER_ROLES = 'x-authorization-roles';
    public const AUTH_HEADER_USER_ID = 'x-authorization-user-id';

    public const ROLE_USER = 'user';
    public const ROLE_CONTENT_MANAGER = 'cm';

    private $registry;

    public function __construct(ApplicationRegistry $applicationRegistry)
    {
        $this->registry = $applicationRegistry;
    }

    /**
     * @return array
     * @throws ApplicationRegistryException
     */
    public function toArray() : array
    {
        return array_filter([
            self::AUTH_HEADER_USER_ID => $this->registry->get(self::REGISTRY_KEY_AUTH_HEADER_USER_ID),
            self::AUTH_HEADER_ROLES => $this->registry->get(self::REGISTRY_KEY_AUTH_HEADER_ROLES),
        ]);
    }
}