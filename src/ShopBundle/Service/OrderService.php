<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\User;
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

    public function __construct(FlashBagInterface $flashBag, ProductServiceInterface $productService)
    {
        $this->flashBag = $flashBag;
        $this->productService=$productService;
    }

    public function getOrderTotalPrice(Order $order): float
    {

    }


    public function confirmOrder(User $user, array $orders):bool
    {
        $chosenProducts=$this->productService->productHandler($orders);
        $productsInCart=$user->getCart();

        if (count($productsInCart)===0){
            $this->flashBag->add('danger','Your Cart is empty!');
            return false;
        }

        foreach ($chosenProducts as $product){
            if (!$productsInCart->contains($product)){
                $this->flashBag->add('danger',"{$product->getName()} does not exist in your cart!");
                return false;
            }
        }

        





    }
}