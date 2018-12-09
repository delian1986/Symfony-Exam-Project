<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\InitialCash;

interface InitialCashServiceInterface
{
    public function getInitialCashValue():float ;

    public function getRecordsCount():int;

    public function getOldInitialCashValue():?InitialCash;

    public function remove(InitialCash $initialCash):void;

    public function insertInitialCash(InitialCash $initialCash):void;

}