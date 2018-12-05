<?php


namespace ShopBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use ShopBundle\Entity\User;
use Doctrine\Common\Persistence\ManagerRegistry;

class UserService implements UserServiceInterface
{
    private $manager;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $manager)
    {
        $this->entityManager = $entityManager;
        $this->manager = $manager;
    }

    public function isFirstRegistration(): bool
    {
        $allRegisteredUsers=count($this->manager->getRepository(User::class)
                                    ->findAll());

        if (0 > ($allRegisteredUsers)) {
            return false;
        }

        return true;
    }

}