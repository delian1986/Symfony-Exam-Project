<?php


namespace ShopBundle\Service;

use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderStatus;
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
        $this->orderStatusRepository=$orderStatusRepository;
    }


    public function findOneOrderByStatus(OrderStatus $status, User $user): ?Order
    {
        return $this->orderRepository->findOneByStatus($status,$user);
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
}