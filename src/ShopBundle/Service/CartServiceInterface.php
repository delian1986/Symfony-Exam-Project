<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrdersProducts;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;

interface CartServiceInterface
{

    public function addToCart(Product $product, User $user, $quantity): void;

    public function addProductToCurrentOpenOrder(Product $product, $quantity, Order $order): void;

    public function itemsInCart(User $user): ?Order;

    public function editItemQuantity(User $user, OrdersProducts $product, int $quantity): bool;

    public function removeFromCart(User $user, OrdersProducts $product): bool;

    public function checkout(User $user):bool ;


}