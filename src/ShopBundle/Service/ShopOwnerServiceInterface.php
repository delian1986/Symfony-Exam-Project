<?php


namespace ShopBundle\Service;



use ShopBundle\Entity\ShopOwner;

interface ShopOwnerServiceInterface
{
    public function setShopOwner(ShopOwner $shopOwner):void;
}