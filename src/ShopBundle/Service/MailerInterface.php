<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Order;
use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;

interface MailerInterface
{
    public function sendRegistration(User $recipient);

    public function sendCartCheckOut(Order $order);

    public function sendCheckOutToAdmin(User $user, Order $order);

    public function sendDeclinedOrderNotify(Order $order,string $reason);

    public function sendShopOwnerNotice(User $oldOwner, User $newOwner);

    public function sendNotifyToWishList(User $user, Product $product);

}