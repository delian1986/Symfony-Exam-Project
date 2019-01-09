<?php


namespace ShopBundle\Service;


use Doctrine\ORM\EntityManagerInterface;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class WishListService implements WishListServiceInterface
{

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(FlashBagInterface $flashBag,EntityManagerInterface $entityManager)
    {
        $this->flashBag=$flashBag;
        $this->entityManager = $entityManager;

    }

    public function addToWishList(Product $product, User $user)
    {
        if ($user->getWishList()->contains($product)) {
            $this->flashBag->add('danger', 'You already have this product in you wish list!');
            return;
        }

        if ($product->getOwner() === $user) {
            $this->flashBag->add('danger', 'You can\'t add your own product to the wish list!');
            return;
        }

        $user->getWishList()->add($product);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->flashBag->add('success', "{$product->getName()} added to your wish list!");
    }

    public function removeFromWishList(Product $product, User $user)
    {
        if (!$user->getWishList()->contains($product)) {
            $this->flashBag->add('danger', 'You don\'t have this product in your wish list!');
            return;
        }

        $user->getWishList()->removeElement($product);
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        $this->flashBag->add('success', "{$product->getName()} removed from your wish list!");
    }

}