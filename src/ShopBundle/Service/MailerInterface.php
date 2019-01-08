<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\User;

interface MailerInterface
{
    public function sendRegistration(User $recipient);

    public function sendCartCheckOut(Order $order);

    public function sendDeclinedOrderNotify(Order $order,string $reason);

    public function sendShopOwnerNotice(User $oldOwner, User $newOwner);

}