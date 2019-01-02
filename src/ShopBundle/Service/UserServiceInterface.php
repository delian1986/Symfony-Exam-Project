<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\User;

interface UserServiceInterface
{
    public function isFirstRegistration():bool;

    public function saveUser(User $user):void ;

    public function findByUsername(string $username):bool ;
}