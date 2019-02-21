<?php

namespace Infrastructure\Models\Auth;

use Symfony\Component\HttpFoundation\Request;

interface RoleExtractor
{
    public function userRoles(Request $request) : array;
}