<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;

interface ProductServiceInterface
{
    public function saveProduct(Product $product):void;

    public function addWatermark(string $image) ;


}