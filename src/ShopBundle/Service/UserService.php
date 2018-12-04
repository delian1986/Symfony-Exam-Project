<?php


namespace ShopBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use ShopBundle\Entity\User;

class UserService implements UserServiceInterface
{

    private $manager;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, \Doctrine\Common\Persistence\ManagerRegistry $manager)
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