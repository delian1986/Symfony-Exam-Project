<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;

interface ProductServiceInterface
{
    public function insertProduct(Product $product):void;
}