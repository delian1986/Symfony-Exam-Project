<?php


namespace ShopBundle\Service;

use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderStatus;
use ShopBundle\Entity\User;

interface OrderServiceInterface
{
    public function findOneOrderByStatus(OrderStatus $status, User $user):?Order;

    public function saveOrder(Order $order);

    public function findOpenOrder(User $user):?Order;

    public function allOrders();

    public function completeOrder(Order $order):bool ;

}