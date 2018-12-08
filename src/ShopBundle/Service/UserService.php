<?php


namespace ShopBundle\Service;

use ShopBundle\Repository\UserRepository;

class UserService implements UserServiceInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function isFirstRegistration(): bool
    {
        $allRegisteredUsers = $this->userRepository->findAll();

        if (null === $allRegisteredUsers) {
            return false;
        }

        return true;
    }
}