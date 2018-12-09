<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Role;

interface RoleServiceInterface
{
    public function getRole(array $role):?Role;
}