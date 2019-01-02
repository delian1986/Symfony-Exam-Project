<?php


namespace ShopBundle\Service;

use ShopBundle\Entity\User;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class UserService implements UserServiceInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(UserRepository $userRepository,FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->flashBag=$flashBag;
    }

    public function isFirstRegistration(): bool
    {
        $allRegisteredUsers = count($this->userRepository->findAll());
        if (0 === $allRegisteredUsers) {
            return true;
        }
        return false;
    }

    /**
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveUser(User $user): void
    {
        $this->userRepository->save($user);
        $this->flashBag->add("success", "{$user->getUsername()} have registered successfully!");

    }

    public function findByUsername(string $username): bool
    {
        $user=$this->userRepository->findBy(['email'=>$username]);

        if (0 === count($user)){
            return false;
        }

        return true;
    }


}