<?php


namespace ShopBundle\Service;



use ShopBundle\Entity\ShopOwner;

interface ShopOwnerServiceInterface
{
    public function changeShopOwner(ShopOwner $shopOwner):void;

    public function saveShopOwner(ShopOwner $shopOwner):void ;
}

