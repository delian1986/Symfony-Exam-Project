<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;

interface CartServiceInterface
{
    public function addToCart(Product $product, User $user):bool;


}