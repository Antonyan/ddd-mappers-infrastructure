<?php

namespace Infrastructure\Models\Auth;

use Symfony\Component\HttpFoundation\Request;

class AuthorizationRequestHeaders
{

    /**
     * @var Request
     */
    private $request;

    /**
     * AuthorizationRequestHeaders constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->request->headers->get(AuthorizationHeaders::AUTH_HEADER_USER_ID, 0);
    }

    /**
     * @return bool
     */
    public function hasUserId(): bool
    {
        return $this->request->headers->has(AuthorizationHeaders::AUTH_HEADER_USER_ID);
    }

    /**
     * @return string
     */
    public function getRoles(): string
    {
        return $this->request->headers->get(AuthorizationHeaders::AUTH_HEADER_ROLES, '');
    }

    /**
     * @return bool
     */
    public function hasRoles(): bool
    {
        return $this->request->headers->has(AuthorizationHeaders::AUTH_HEADER_ROLES);
    }
}