<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;

interface RoleServiceInterface
{
    public function getRole(array $role):?Role;

    public function setRoleByName(array $role, User $user);
}