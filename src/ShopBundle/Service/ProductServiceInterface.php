<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;

interface ProductServiceInterface
{
    public function saveProduct(Product $product):void;

    /**
     * @param array $chosenProducts | Product[]
     * @return array | Product[]
     */
    public function productHandler(array $chosenProducts):array ;

    public function handleImage(string $image):string;

    public function addWatermark(string $image) ;
}