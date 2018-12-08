<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\LineItem;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class UserController
 * @package ShopBundle\Controller
 * @Route("user")
 */
class UserController extends Controller
{
    /**
     * @Route("/orders",name="my_items")
     */
    public function myItemsAction()
    {
        /** @var User $user */
        $user=$this->getUser();
        $orders=$user->getOrders();
        return $this->render('user/orders.html.twig',['orders'=>$orders]);
    }

    /**
     * @Route("/resell/{id}",name="item_resell")
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function itemResellAction(){
        /** @var User $user */
        $user=$this->getUser();
        $products=$user->getCart();

        /** @var Product $product */
        foreach ($products as $product){
            $newProductToSell=new Product();
        }

        return $this->redirectToRoute('homepage');
    }
}
