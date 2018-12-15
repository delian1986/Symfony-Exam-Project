<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrdersProducts;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderProductsRepository;
use ShopBundle\Repository\OrderRepository;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartService implements CartServiceInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /** @var OrderRepository */
    private $orderRepository;

    /** @var OrderStatusServiceInterface */
    private $orderStatusService;

    /** @var OrderProductsRepository */
    private $orderProductsRepository;

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(UserRepository $userRepository,
                                ProductServiceInterface $productService,
                                OrderRepository $orderRepository,
                                OrderStatusServiceInterface $orderStatusService,
                                OrderProductsRepository $orderProductsRepository,
                                FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->orderStatusService = $orderStatusService;
        $this->orderProductsRepository = $orderProductsRepository;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Product $product
     * @param User $user
     * @param $quantity
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addToCart(Product $product, User $user, $quantity): void
    {
        if ($product->getOwner() === $user) {
            $this->flashBag->add('danger', 'You can\'t add your own product to the cart!');
            return;
        }

        $openStatus = $this->orderStatusService->findStatus(['name' => 'Open']);

        /** @var Order $userOpenOrder */
        $userOpenOrder = $this->orderRepository->findOneBy(['status' => $openStatus]);

        if ($userOpenOrder) {
            /**@var Order $userOpenOrder */
            foreach ($userOpenOrder->getProducts() as $productInCart) {
                if ($productInCart->getProduct()->getId() === $product->getId()) {
                    $this->flashBag->add('danger', "You already have {$product->getName()} in you cart!");
                    return;
                }
            }
            $this->addProductToCurrentOpenOrder($product, $quantity, $userOpenOrder);

        } else {
            $order = new Order();
            $order->setStatus($openStatus);
            $order->setUser($user);

            $this->addProductToCurrentOpenOrder($product, $quantity, $order);
        }

    }

    /**
     * @param Product $product
     * @param $quantity
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addProductToCurrentOpenOrder(Product $product, $quantity, Order $order): void
    {
        $productOrder = new OrdersProducts();
        $productOrder
            ->setProduct($product)
            ->setQuantity(intval($quantity))
            ->setOrders($order);
        $order->getProducts()->add($productOrder);
        $this->orderRepository->save($order);
        $this->flashBag->add('success', "{$product->getName()} added to your cart!");
    }


    public function numberOfItemsInCart(User $user): int
    {
        $openStatus = $this->orderStatusService->findStatus(['name' => 'Open']);

        /** @var Order $userOpenOrder */
        $userOpenOrder = $this->orderRepository->findOneBy(['status' => $openStatus]);

        if ($userOpenOrder->getProducts()){
            return $userOpenOrder->getProducts()->count();
        }

        return 0;

    }
}