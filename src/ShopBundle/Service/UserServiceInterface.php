<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\User;

interface UserServiceInterface
{
    public function isFirstRegistration():bool;

    public function registerUser(User $user):void ;
}