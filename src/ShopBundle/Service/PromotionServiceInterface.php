<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Promotion;

interface PromotionServiceInterface
{
    public function addPromotion(Promotion $promotion):void ;
}