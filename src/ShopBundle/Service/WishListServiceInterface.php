<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Entity\User;

interface WishListServiceInterface
{
    public function addToWishList(Product $product, User $user);

    public function removeFromWishList(Product $product, User $user);
}