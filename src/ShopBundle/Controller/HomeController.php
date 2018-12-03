<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction()
    {
        $activeProducts=$this->getDoctrine()->getRepository(Product::class)->findAllInStock();

        return $this->render('home/index.html.twig',['products'=>$activeProducts]);
    }
}
