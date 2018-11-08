<?php
namespace Infrastructure\Models;

class ContainerBuilder extends \Symfony\Component\DependencyInjection\ContainerBuilder
{
    public function register($id, $class = null, $public = true)
    {
        return parent::register($id, $class)->setPublic($public);
    }
}