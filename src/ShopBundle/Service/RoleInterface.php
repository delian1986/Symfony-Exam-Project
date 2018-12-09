<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Role;
use ShopBundle\Repository\RoleRepository;

class RoleInterface implements RoleServiceInterface
{
    private $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository=$roleRepository;
    }

    public function getRole(array $role): ?Role
    {
        return $this->roleRepository->getRoleByName($role);
    }
}