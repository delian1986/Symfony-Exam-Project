<?php

namespace ShopBundle\Controller;

use ShopBundle\Entity\Product;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProductController
 * @package ShopBundle\Controller
 * @Route("product")
 */
class ProductController extends Controller
{
    /**
     * @Route("/details/{slug}", name="product_details")
     * @param Product $product
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function productDetails(Product $product)
    {
        return $this->render('product/details.html.twig',['product'=>$product]);
    }
}
