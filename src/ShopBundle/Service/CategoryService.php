<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Category;
use ShopBundle\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class CategoryService implements CategoryServiceInterface
{
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(CategoryRepository $categoryRepository, FlashBagInterface $flashBag)
    {
        $this->categoryRepository=$categoryRepository;
        $this->flashBag=$flashBag;
    }

    /**
     * @param Category $category
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveCategory(Category $category): void
    {
       $this->categoryRepository->saveCategory($category);
        $this->flashBag->add('success', "{$category->getName()} added successfully!");
    }

    public function all()
    {
        return $this->categoryRepository->findAll();
    }


}