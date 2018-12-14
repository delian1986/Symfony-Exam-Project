<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrdersProducts;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderProductsRepository;
use ShopBundle\Repository\OrderRepository;
use ShopBundle\Repository\OrderStatusRepository;
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
        $this->orderProductsRepository=$orderProductsRepository;
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
        $openStatus = $this->orderStatusService->findStatus(['name' => 'Open']);

        /** @var Order $userOrders */
        $userOpenOrder = $this->orderRepository->findBy(['status' => $openStatus]);
        if ($userOpenOrder) {

        } else {
            $order = new Order();
            $order->setStatus($openStatus);
            $order->setUser($user);
            $this->orderRepository->save($order);

            $productOrder = new OrdersProducts();
            $productOrder
                ->setProduct($product)
                ->setQuantity(intval($quantity))
                ->setOrders($order);
            $order->getProducts()->add($productOrder);
            $this->orderRepository->save($order);
        }

        $this->flashBag->add('success', "{$product->getName()} added to your cart!");
    }

    public function checkoutPreview(User $user, array $chosenProducts): array
    {
        $products = $this->productService->productHandler($chosenProducts);

        return $products;
    }


}