<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
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
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(UserRepository $userRepository,
                                ProductService $productService,
                                FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->productService = $productService;
        $this->flashBag = $flashBag;
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

    public function checkoutPreview(User $user, array $chosenProducts): array
    {
        $products = $this->productService->productHandler($chosenProducts);

        return $products;
    }


}