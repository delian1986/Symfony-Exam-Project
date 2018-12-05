<?php


namespace ShopBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use ShopBundle\Entity\Product;
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

    public function addToCart(Product $product, User $user): bool
    {
        $user->getCart()->add($product);
        $em=$this->manager->getManager();
        $em->persist($product);
        $em->flush();

        return true;
    }
}