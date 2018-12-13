<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\User;

interface OrderServiceInterface
{
    public function getOrderTotalPrice(array $chosenProductWithQuantities):float ;

    public function confirmOrder(User $user, array $products):bool;

    public function createOrder(User $user,array $products, float $totalPrice );

}