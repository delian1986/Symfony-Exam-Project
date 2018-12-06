<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\LineItem;
use ShopBundle\Entity\User;

interface CartServiceInterface
{
    public function addToCart(LineItem $lineItem, User $user):void;


}