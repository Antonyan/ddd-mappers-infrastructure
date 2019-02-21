<?php

namespace Infrastructure\Services\Auth;

use Symfony\Component\HttpFoundation\Request;

interface Policy
{
    public function check(Request $request) : bool;
}