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
use ShopBundle\Repository\UserRepository;
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
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var OrderRepository
     */
    private $orderRepository;

    /**
     * @var MailerInterface
     */
    private $mailer;

    public function __construct(FlashBagInterface $flashBag,
                                ProductServiceInterface $productService,
                                OrderRepository $orderRepository,
                                ProductRepository $productRepository,
                                OrderStatusRepository $orderStatusRepository,
                                UserServiceInterface $userService,
                                UserRepository $userRepository,
                                MailerInterface $mailer)
    {
        $this->flashBag = $flashBag;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
        $this->productRepository = $productRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->userRepository = $userRepository;
        $this->mailer=$mailer;
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

    public function findAllOrders()
    {
        return $this->orderRepository->findAllOrders();
    }

    public function allOrdersByStatusName(string $status)
    {
        $statusObj = $this->orderStatusRepository->findOneByName(ucfirst($status));
        return $this->orderRepository->findAllByStatus($statusObj);
    }

    /**
     * @param Order $order
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function completeOrder(Order $order): bool
    {
        $userBalance = $order->getUser()->getBalance();

        if ($userBalance < $order->getTotal()) {
            $reason='Your balance is too low to complete this order';
            $this->declineOrder($order,$reason);

            return false;
        }

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

            $this->productRepository->save($product);

            $productFromDB->setQuantity($productFromDB->getQuantity() - $quantity);
            $this->productRepository->save($productFromDB);

            $user->setBalance($user->getBalance() - $orderProduct->getProductTotalPrice());
            $user->setMoneySpent($user->getMoneySpent() + $orderProduct->getProductTotalPrice());

            $owner->setBalance($owner->getBalance() + $orderProduct->getProductTotalPrice());
            $owner->setMoneyReceived($owner->getMoneyReceived() + $orderProduct->getProductTotalPrice());
            $this->userRepository->save($user);
            $this->userRepository->save($owner);

        }
        $completeStatus = $this->orderStatusRepository->findOneByName('Complete');
        $order->setStatus($completeStatus);
        $this->orderRepository->save($order);
        $this->flashBag->add('success', "{$order->getId()} successfully completed!");
        $this->mailer->sendCartCheckOut($order);

        return true;
    }

    public function declineOrder(Order $order,string $reason): bool
    {
        $declinedStatus = $this->orderStatusRepository->findOneByName('Declined');
        $order->setStatus($declinedStatus);

        $this->orderRepository->save($order);
        $this->mailer->sendDeclinedOrderNotify($order,$reason);

        return true;
    }


}