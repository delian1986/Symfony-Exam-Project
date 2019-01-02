<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Category;

interface CategoryServiceInterface
{
    public function saveCategory(Category $category):void;

    public function all();
}