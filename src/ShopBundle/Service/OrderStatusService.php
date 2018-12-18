<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\OrderStatus;
use ShopBundle\Repository\OrderStatusRepository;

class OrderStatusService implements OrderStatusServiceInterface
{
    /** @var OrderStatusRepository */
    private $orderStatusRepository;

    public function __construct(OrderStatusRepository $orderStatusRepository)
    {
        $this->orderStatusRepository=$orderStatusRepository;
    }

    public function findOneByStatusName(string $statusName): OrderStatus
    {
        return $this->orderStatusRepository->findOneByName($statusName);
    }
}