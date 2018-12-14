<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\OrderStatus;

interface OrderStatusServiceInterface
{
    public function findStatus(array $status):OrderStatus;
}