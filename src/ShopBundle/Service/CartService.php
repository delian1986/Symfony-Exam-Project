<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrdersProducts;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use ShopBundle\Repository\OrderProductsRepository;
use ShopBundle\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CartService implements CartServiceInterface
{

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var OrderServiceInterface
     */
    private $orderService;

    /** @var OrderStatusServiceInterface */
    private $orderStatusService;

    /** @var OrderProductsRepository */
    private $orderProductsRepository;

    /**
     * @var ProductServiceInterface
     */
    private $productService;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(UserRepository $userRepository,
                                ProductServiceInterface $productService,
                                OrderServiceInterface $orderService,
                                OrderStatusServiceInterface $orderStatusService,
                                OrderProductsRepository $orderProductsRepository,
                                FlashBagInterface $flashBag,
                                MailerInterface $mailer)
    {
        $this->userRepository = $userRepository;
        $this->productService = $productService;
        $this->orderStatusService = $orderStatusService;
        $this->orderService = $orderService;
        $this->orderProductsRepository = $orderProductsRepository;
        $this->flashBag = $flashBag;
        $this->mailer=$mailer;
    }

    /**
     * @param Product $product
     * @param User $user
     * @param $quantity
     */
    public function addToCart(Product $product, User $user, $quantity): void
    {
        if ($product->getOwner() === $user) {
            $this->flashBag->add('danger', 'You can\'t add your own product to the cart!');
            return;
        }

        $openStatus = $this->orderStatusService->findOneByStatusName('Open');
        //find the order with Open status
        /** @var Order $userOpenOrder */
        $userOpenOrder = $this->orderService->findOneOrderByStatus($openStatus, $user);

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
     */
    public function addProductToCurrentOpenOrder(Product $product, $quantity, Order $order): void
    {
        $productOrder = new OrdersProducts();
        $productOrder
            ->setProduct($product)
            ->setQuantity(intval($quantity))
            ->setOrders($order);
        $order->getProducts()->add($productOrder);
        $this->orderService->saveOrder($order);
        $this->flashBag->add('success', "{$product->getName()} added to your cart!");
    }

    public function itemsInCart(User $user): ?Order
    {
        $openStatus = $this->orderStatusService->findOneByStatusName('Open');
        $userOpenOrder = $this->orderService->findOneOrderByStatus($openStatus, $user);

        if (null === $userOpenOrder) {
            return null;
        }
        /** @var OrdersProducts $order */
        foreach ($userOpenOrder->getProducts() as $order){
            if (false===$order->getProduct()->isListed()){
                $this->orderProductsRepository->remove($order);
                $this->flashBag->add('danger',"We are sorry, but {$order->getProduct()->getName()} is not in the store at this moment!");
            }
        }

        return $userOpenOrder;

    }

    /**
     * @param User $user
     * @param OrdersProducts $product
     * @param int $quantity
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function editItemQuantity(User $user, OrdersProducts $product, int $quantity): bool
    {

        $product->setQuantity($quantity);
        $this->orderProductsRepository->save($product);
        $this->flashBag->add('success', "Product {$product->getProduct()->getName()} quantity edited!");
        return true;

    }

    /**
     * @param User $user
     * @param OrdersProducts $product
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function removeFromCart(User $user, OrdersProducts $product): bool
    {
        $this->orderProductsRepository->remove($product);

        $this->flashBag->add('success', "{$product->getProduct()->getName()} was removed from your cart!");
        return true;
    }

    /**
     * @param User $user
     * @return bool
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function checkout(User $user): bool
    {
        $openStatus = $this->orderStatusService->findOneByStatusName('Open');
        $userOpenOrder = $this->orderService->findOneOrderByStatus($openStatus, $user);

        if (null===$userOpenOrder || 0=== $userOpenOrder->getProducts()->count()){
            $this->flashBag->add('danger','Your cart is empty!');
            return false;
        }

        if ($userOpenOrder->getTotal() > $user->getBalance() || $userOpenOrder->getTotal() <0 ) {
            $this->flashBag->add('danger', 'Your balance is too low to complete the order!');
            return false;
        }

        $this->setPriceToCheckoutProducts($userOpenOrder);

        $pendingStatus = $this->orderStatusService->findOneByStatusName('Pending');

        $userOpenOrder->setStatus($pendingStatus);
        $userOpenOrder->setDateCreated(new \DateTime("now"));

        $this->orderService->saveOrder($userOpenOrder);

        $this->mailer->sendCartCheckOut($userOpenOrder);
        $this->mailer->sendCheckOutToAdmin($user,$userOpenOrder);

        $this->flashBag->add('success', 'Your order was received.');
        return true;
    }

    /**
     * @param Order $order
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function setPriceToCheckoutProducts(Order $order){
        foreach ($order->getProducts() as $product){
            $product->setPrice($product->getProduct()->getPrice());
            $this->orderProductsRepository->save($product);
        }
    }


}