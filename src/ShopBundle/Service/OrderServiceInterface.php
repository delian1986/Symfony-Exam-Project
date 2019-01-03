<?php


namespace ShopBundle\Service;

use ShopBundle\Entity\Order;
use ShopBundle\Entity\OrderStatus;
use ShopBundle\Entity\User;

interface OrderServiceInterface
{
    public function findOneOrderByStatus(OrderStatus $status, User $user):?Order;

    public function saveOrder(Order $order);

    public function findAllOrders();

    public function allOrdersByStatusName(string $status);

    public function completeOrder(Order $order):bool ;

    public function declineOrder(Order $order, string $reason):bool ;

}