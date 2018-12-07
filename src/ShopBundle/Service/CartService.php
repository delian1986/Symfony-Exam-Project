<?php


namespace ShopBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use ShopBundle\Entity\LineItem;
use Doctrine\Common\Persistence\ManagerRegistry;
use ShopBundle\Entity\User;

class CartService implements CartServiceInterface
{

    private $manager;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, ManagerRegistry $manager)
    {
        $this->entityManager = $entityManager;
        $this->manager = $manager;
    }

    public function addToCart(LineItem $lineItem, User $user): void
    {
        $user->getCart()->add($lineItem);
        $em=$this->manager->getManager();
        $em->persist($lineItem);
        $em->flush();
    }
}