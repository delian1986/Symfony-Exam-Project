<?php


namespace ShopBundle\Service;



use ShopBundle\Entity\ShopOwner;
use ShopBundle\Entity\User;

interface ShopOwnerServiceInterface
{
    public function changeShopOwner(ShopOwner $shopOwner):void;

    public function saveShopOwner(ShopOwner $shopOwner):void ;

    public function getOwner():User;
}

