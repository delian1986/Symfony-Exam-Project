<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;

interface CartServiceInterface
{

    public function addToCart(Product $product, User $user, $quantity):void;

    public function addProductToCurrentOpenOrder(Product $product, $quantity, Order $order): void;

    public function numberOfItemsInCart(User $user):int ;


}