<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)
                ->findAllTopSellers();

        return $this->render('home/index.html.twig',['products'=>$products]);
    }
}
