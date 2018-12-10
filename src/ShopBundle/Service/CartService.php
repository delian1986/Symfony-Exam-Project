<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartService implements CartServiceInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(UserRepository $userRepository, FlashBagInterface $flashBag)
    {
        $this->userRepository=$userRepository;
        $this->flashBag=$flashBag;
    }

    /**
     * @param Product $product
     * @param User $user
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addToCart(Product $product, User $user): void
    {
        $user->getCart()->add($product);
        $this->userRepository->save($user);

        $this->flashBag->add('success', "{$product->getName()} added to your cart!");
    }
}