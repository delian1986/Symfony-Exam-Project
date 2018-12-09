<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProductService implements ProductServiceInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(ProductRepository $productRepository,FlashBagInterface $flashBag)
    {
        $this->productRepository=$productRepository;
        $this->flashBag=$flashBag;
    }

    /**
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertProduct(Product $product): void
    {
        $this->productRepository->save($product);

        $this->flashBag->add('success', "{$product->getName()} successfully saved!");
    }
}