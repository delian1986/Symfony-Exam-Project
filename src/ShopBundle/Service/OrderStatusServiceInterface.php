<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\OrderStatus;

interface OrderStatusServiceInterface
{
    public function findOneByStatusName(string $status):OrderStatus;
}