<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderRepository;
use ShopBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class OrderService implements OrderServiceInterface
{
    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var UserServiceInterface
     */
    private $userService;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    public function __construct(FlashBagInterface $flashBag,
                                ProductServiceInterface $productService,
                                OrderRepository $orderRepository,
                                ProductRepository $productRepository,
                                UserServiceInterface $userService)
    {
        $this->flashBag = $flashBag;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
        $this->productRepository = $productRepository;
    }


    public function getOrderTotalPrice(array $chosenProductWithQuantities): float
    {
        $sum = array_reduce($chosenProductWithQuantities, function ($i, $product) {
            return $i += $product->getProductTotalPrice();
        });

        return $sum;
    }


    /**
     * @param User $user
     * @param array $productsWithQuantities
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function confirmOrder(User $user, array $productsWithQuantities): bool
    {
        $chosenProducts = $this->productService->productHandler($productsWithQuantities);
        $productsInCart = $user->getCart();
        $totalPrice = $this->getOrderTotalPrice($chosenProducts);
        $userBalance = $user->getBalance();

        if (count($productsInCart) === 0) {
            $this->flashBag->add('danger', 'Your Cart is empty!');
            return false;
        }

        if ($userBalance < $totalPrice) {
            $this->flashBag->add('danger', 'Your don\'t have enough money to complete your order! ');
            return false;
        }

        foreach ($chosenProducts as $product) {
            if (!$productsInCart->contains($product)) {
                $this->flashBag->add('danger', "{$product->getName()} does not exist in your cart!");
                return false;
            }
        }

        /** @var Product $product */
        foreach ($productsInCart as $product) {
            /** @var Product $productFromDB */
            $productFromDB = $this->productRepository->find($product->getId());
            $productFromDB->setQuantity($productFromDB->getQuantity() - $product->getQuantity());
            $user->setBalance($user->getBalance() - $product->getProductTotalPrice());
            $user->getCart()->removeElement($product);

            /**
             * @var User $seller
             */
            $seller = $product->getOwner();
            $seller->setBalance($seller->getBalance() + $product->getProductTotalPrice());
            $seller->setMoneyReceived($seller->getMoneyReceived() + $product->getProductTotalPrice());
            $user->setMoneySpent($user->getMoneySpent() + $totalPrice);
            $this->userService->saveUser($seller);
            $this->userService->saveUser($user);
            $this->productRepository->save($productFromDB);
        }

        $orderedProducts = $this->createOrder($user, $productsWithQuantities, $totalPrice);

        $this->orderRepository->save($orderedProducts);

        $this->flashBag->add('success', "{$user->getFullName()}, your order has beer received");

        return true;
    }

    public function createOrder(User $user, array $products, float $totalPrice)
    {
        $order = new Order();
        $order->setUser($user);
        $order->setProducts($products);
        $order->setTotal($totalPrice);

        return $order;
    }
}