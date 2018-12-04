<?php


namespace ShopBundle\Service;


interface UserServiceInterface
{
    public function isFirstRegistration():bool;
}