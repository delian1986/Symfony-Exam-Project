<?php

namespace ShopBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use ShopBundle\Entity\User;
use ShopBundle\Service\ProductServiceInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package ShopBundle\Controller
 * @Route("user")
 * @Security("is_granted('IS_AUTHENTICATED_FULLY')")

 */
class UserController extends Controller
{
    /**
     * @var ProductServiceInterface
     */
    private $productService;

    public function __construct(ProductServiceInterface $productService)
    {
        $this->productService=$productService;
    }

    /**
     * @Route("/orders",name="my_orders")
     */
    public function myOrdersAction()
    {
        /** @var User $user */
        $user=$this->getUser();
        $orders=$user->getOrders();

        foreach ($orders as $order){
            $products=$this->productService->productHandler($order->getProducts());
            $order->setProducts($products);
        }

        return $this->render('user/orders.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/products",name="my_products")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function myProductsAction(){
        /** @var User $user */
        $user= $this->getUser();
        $myProducts=$user->getMyProducts();

        return $this->render('user/products.html.twig',['products'=>$myProducts]);
    }
}
