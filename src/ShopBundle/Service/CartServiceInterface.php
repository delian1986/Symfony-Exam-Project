<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;

interface CartServiceInterface
{

    public function addToCart(Product $product, User $user):void;

    public function checkoutPreview(User $user, array $products):Order ;

}