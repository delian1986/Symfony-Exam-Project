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
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(UserRepository $userRepository,
                                ProductServiceInterface $productService,
                                OrderServiceInterface $orderService,
                                OrderStatusServiceInterface $orderStatusService,
                                OrderProductsRepository $orderProductsRepository,
                                FlashBagInterface $flashBag)
    {
        $this->userRepository = $userRepository;
        $this->productService = $productService;
        $this->orderStatusService = $orderStatusService;
        $this->orderService = $orderService;
        $this->orderProductsRepository = $orderProductsRepository;
        $this->flashBag = $flashBag;
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


    public function numberOfItemsInCart(User $user): int
    {
        $openStatus = $this->orderStatusService->findOneByStatusName('Open');

        /** @var Order $userOpenOrder */
        $userOpenOrder = $this->orderService->findOneOrderByStatus($openStatus, $user);

        if ($userOpenOrder->getProducts()) {
            return $userOpenOrder->getProducts()->count();
        }

        return 0;

    }

    public function itemsInCart(User $user): ?Order
    {
        $openStatus = $this->orderStatusService->findOneByStatusName('Open');
        $userOpenOrder = $this->orderService->findOneOrderByStatus($openStatus, $user);

        if (null === $userOpenOrder) {
            return null;
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
        $this->flashBag->add('success', "{$product->getProduct()->getName()} edited!");
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

    public function checkout(User $user): bool
    {
        $openStatus = $this->orderStatusService->findOneByStatusName('Open');
        $userOpenOrder = $this->orderService->findOneOrderByStatus($openStatus, $user);

        if ($userOpenOrder->getTotal()>$user->getBalance()){
            $this->flashBag->add('error','Your balance is too low to complete the order!');
            return false;
        }

        /** @var OrdersProducts $orderProduct */
        foreach ($userOpenOrder->getProducts() as $orderProduct) {
            $productFromDB = $orderProduct->getProduct();
            $quantity = $orderProduct->getQuantity();

            if($quantity>$productFromDB->getQuantity()){
                $this->flashBag->add('error', "There is not enough quantity of {$productFromDB->getName()}");
                return false;
            }
            $owner = $orderProduct->getProduct()->getOwner();
            $price = $orderProduct->getProductTotalPrice();

            $product = new Product();
            $product->setQuantity($quantity);
            $product->setIsListed(false);
            $product->setImage($productFromDB->getImage());
            $product->setOwner($user);
            $product->setPrice($productFromDB->getPrice());
            $product->setDescription($productFromDB->getDescription());
            $product->setName($productFromDB->getName());
            $product->setCategory($productFromDB->getCategory());

//            var_dump($product);exit();

            $this->productService->saveProduct($product);

            $productFromDB->setQuantity($productFromDB->getQuantity() - $quantity);
            $this->productService->saveProduct($productFromDB);

            $user->setBalance($user->getBalance() - $orderProduct->getProductTotalPrice());
            $user->setMoneySpent($user->getMoneySpent() + $orderProduct->getProductTotalPrice());

            $owner->setBalance($owner->getBalance() + $orderProduct->getProductTotalPrice());
            $owner->setMoneyReceived($owner->getMoneyReceived() + $orderProduct->getProductTotalPrice());
            $this->userRepository->save($user);
            $this->userRepository->save($owner);

        }
        $pendingStatus=$this->orderStatusService->findOneByStatusName('Pending');
        
        $userOpenOrder->setStatus($pendingStatus);

        $this->orderService->saveOrder($userOpenOrder);
        return true;
    }


}