<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\User;

interface OrderServiceInterface
{
    public function getOrderTotalPrice(Order $order):float ;

    public function confirmOrder(User $user, array $products):bool;
}