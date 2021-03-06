<?php


namespace ShopBundle\Service;

use Knp\Component\Pager\PaginatorInterface;
use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrdersProducts;
use ShopBundle\Entity\OrderStatus;
use ShopBundle\Entity\SoldProduct;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderRepository;
use ShopBundle\Repository\OrderStatusRepository;
use ShopBundle\Repository\ProductRepository;
use ShopBundle\Repository\SoldProductRepository;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
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

    /**
     * @var SoldProductRepository
     */
    private $soldProductsRepository;

    /**
     * @var PaginatorInterface
     */
    private $paginator;

    /**
     * @var RequestStack
     */
    private $request;

    public function __construct(FlashBagInterface $flashBag,
                                ProductServiceInterface $productService,
                                OrderRepository $orderRepository,
                                ProductRepository $productRepository,
                                OrderStatusRepository $orderStatusRepository,
                                UserServiceInterface $userService,
                                UserRepository $userRepository,
                                MailerInterface $mailer,
                                SoldProductRepository $soldProductRepository,
                                PaginatorInterface $paginator,
                                RequestStack $request)
    {
        $this->flashBag = $flashBag;
        $this->productService = $productService;
        $this->orderRepository = $orderRepository;
        $this->userService = $userService;
        $this->productRepository = $productRepository;
        $this->orderStatusRepository = $orderStatusRepository;
        $this->userRepository = $userRepository;
        $this->mailer = $mailer;
        $this->soldProductsRepository = $soldProductRepository;
        $this->paginator=$paginator;
        $this->request=$request;
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
        $products = $this->paginator->paginate(
            $this->orderRepository->findAllOrders(),
            $this->request->getCurrentRequest()->query->getInt('page', 1),
            9);
        return $products;
    }

    public function allOrdersByStatusName(string $status)
    {
        $statusObj = $this->orderStatusRepository->findOneByName(ucfirst($status));
        $products = $this->paginator->paginate(
            $this->orderRepository->findAllByStatus($statusObj),
            $this->request->getCurrentRequest()->query->getInt('page', 1),
            9);
        return $products;
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
            $reason = 'Your balance is too low to complete this order';
            $this->declineOrder($order, $reason);

            return false;
        }

        /** @var OrdersProducts $orderProduct */
        foreach ($order->getProducts() as $orderProduct) {
            $productFromDB = $orderProduct->getProduct();
            $quantity = $orderProduct->getQuantity();

            if ($quantity > $productFromDB->getQuantity()) {
                $this->flashBag->add('danger', "There is not enough quantity of {$productFromDB->getName()}");
                return false;
            }
            $owner = $orderProduct->getProduct()->getOwner();
            $price = $orderProduct->getProductTotalPrice();
            /** @var User $user */
            $user = $order->getUser();

            $soldProduct = new SoldProduct();
            $soldProduct
                ->setProduct($productFromDB)
                ->setPrice($price)
                ->setQuantity($quantity)
                ->setOwner($user);

            $this->soldProductsRepository->save($soldProduct);


            $productFromDB->setQuantity($productFromDB->getQuantity() - $quantity);
            $productFromDB->setSoldTimes($productFromDB->getSoldTimes() + $quantity);
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
        $order->setDateCreated(new \DateTime("now"));
        $this->orderRepository->save($order);
        $this->flashBag->add('success', "Order with ID: {$order->getId()} was successfully completed!");
        $this->mailer->sendCartCheckOut($order);

        return true;
    }

    /**
     * @param Order $order
     * @param string $reason
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function declineOrder(Order $order, string $reason): bool
    {
        $declinedStatus = $this->orderStatusRepository->findOneByName('Declined');
        $order->setStatus($declinedStatus);
        $this->flashBag->add('danger', "Order {$order->getId()} was declined!");
        $this->orderRepository->save($order);
        $this->mailer->sendDeclinedOrderNotify($order, $reason);

        return true;
    }


}