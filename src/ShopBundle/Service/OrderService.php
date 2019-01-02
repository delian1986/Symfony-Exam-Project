<?php


namespace ShopBundle\Service;

use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrdersProducts;
use ShopBundle\Entity\OrderStatus;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderRepository;
use ShopBundle\Repository\OrderStatusRepository;
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
     * @var OrderStatusRepository
     */
    private $orderStatusRepository;

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
                                OrderStatusRepository $orderStatusRepository,
                                UserServiceInterface $userService)
    {
        $this->flashBag = $flashBag;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
        $this->productRepository = $productRepository;
        $this->orderStatusRepository = $orderStatusRepository;
    }


    public function findOneOrderByStatus(OrderStatus $status, User $user): ?Order
    {
        return $this->orderRepository->findOneByStatus($status, $user);
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveOrder(Order $order)
    {
        $this->orderRepository->save($order);
    }

    public function findOpenOrder(User $user): ?Order
    {
        // TODO: Implement findOpenOrder() method.
    }

    public function allOrders()
    {
        return $this->orderRepository->findAll();
    }

    /**
     * @param Order $order
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function completeOrder(Order $order): bool
    {
        /** @var OrdersProducts $orderProduct */
        foreach ($order->getProducts() as $orderProduct) {
            $productFromDB = $orderProduct->getProduct();
            $quantity = $orderProduct->getQuantity();

            if ($quantity > $productFromDB->getQuantity()) {
                $this->flashBag->add('error', "There is not enough quantity of {$productFromDB->getName()}");
                return false;
            }
            $owner = $orderProduct->getProduct()->getOwner();
            $price = $orderProduct->getProductTotalPrice();
            /** @var User $user */
            $user = $order->getUser();

            $product = new Product();
            $product->setQuantity($quantity);
            $product->setIsListed(false);
            $product->setImage($productFromDB->getImage());
            $product->setOwner($user);
            $product->setPrice($productFromDB->getPrice());
            $product->setDescription($productFromDB->getDescription());
            $product->setName($productFromDB->getName());
            $product->setCategory($productFromDB->getCategory());

            $this->productService->saveProduct($product);

            $productFromDB->setQuantity($productFromDB->getQuantity() - $quantity);
            $this->productService->saveProduct($productFromDB);

            $user->setBalance($user->getBalance() - $orderProduct->getProductTotalPrice());
            $user->setMoneySpent($user->getMoneySpent() + $orderProduct->getProductTotalPrice());

            $owner->setBalance($owner->getBalance() + $orderProduct->getProductTotalPrice());
            $owner->setMoneyReceived($owner->getMoneyReceived() + $orderProduct->getProductTotalPrice());
            $this->userService->saveUser($user);
            $this->userService->saveUser($owner);

        }
        $completeStatus = $this->orderStatusRepository->findOneByName('Complete');
        $order->setStatus($completeStatus);
        $this->orderRepository->save($order);

        return true;
    }


}