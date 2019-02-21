<?php

namespace Infrastructure\Models\Auth;

class Rule
{
    /**
     * @var string
     */
    private $role;

    /**
     * @var array
     */
    private $policies;

    /**
     * Rule constructor.
     * @param $role
     * @param array $policies
     */
    public function __construct($role, array $policies = [])
    {
        $this->role = $role;
        $this->policies = $policies;
    }

    /**
     * @return string
     */
    public function roleName() : string
    {
        return $this->role;
    }

    /**
     * @return array
     */
    public function policies() : array
    {
        return $this->policies;
    }
}