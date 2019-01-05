<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Entity\Review;
use ShopBundle\Entity\User;

interface ReviewServiceInterface
{
    public function getReviewByUserAndProduct(User $user, Product $product): ?Review;

    public function addReview(Review $review): void;

    public function deleteReview(Review $review):void ;


}