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
     * @param LineItem $lineItem
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function itemResellAction(LineItem $lineItem){
        $user=$this->getUser();
        $product=new Product();
        $product->setName($lineItem->getProduct()->getName());
        $product->setPrice($lineItem->getProduct()->getPrice());
        $product->setOwner($user);
        $product->setCategory($lineItem->getProduct()->getCategory());
        $product->setQuantity(1);
        $product->setDescription($lineItem->getProduct()->getDescription());

        $em=$this->getDoctrine()->getManager();
        $em->persist($product);
        $em->flush();

        return $this->redirectToRoute('homepage');
    }
}
