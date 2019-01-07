<?php

namespace ShopBundle\Controller;

use Knp\Component\Pager\PaginatorInterface;
use ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends Controller
{
    /**
     * @Route("/", name="homepage")
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(PaginatorInterface $paginator, Request $request)
    {
//        $activeProducts=$this->getDoctrine()->getRepository(Product::class)->findAllInStock();

        $products = $paginator->paginate(
            $this->getDoctrine()->getRepository(Product::class)
                ->findAllTopSellers(),
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('home/index.html.twig',['products'=>$products]);
    }
}
