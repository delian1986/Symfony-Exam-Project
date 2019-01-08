<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Role;
use ShopBundle\Entity\User;
use ShopBundle\Repository\RoleRepository;
use ShopBundle\Repository\UserRepository;

class RoleInterface implements RoleServiceInterface
{
    /**
     * @var RoleRepository
     */
    private $roleRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(RoleRepository $roleRepository,
                                UserRepository $userRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->userRepository = $userRepository;
    }

    public function getRole(array $role): ?Role
    {
        return $this->roleRepository->getRoleByName($role);
    }

    public function setRoleByName(array $role, User $user)
    {
            $adminRole = $this->getRole($role);
            $user->addRole($adminRole);
            $this->userRepository->save($user);
    }


}