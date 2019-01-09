<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ProductService implements ProductServiceInterface
{
    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(ProductRepository $productRepository, FlashBagInterface $flashBag, MailerInterface $mailer)
    {
        $this->productRepository = $productRepository;
        $this->flashBag = $flashBag;
        $this->mailer=$mailer;
    }

    /**
     * @param Product $product
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveProduct(Product $product): void
    {
        $this->productRepository->save($product);

        $this->flashBag->add('success', "{$product->getName()} successfully saved!");
    }

    public function notifyUsersWithProductInTheirWishList($userList, Product $product)
    {
        $countOfNotifiedUsers=0;
        foreach ($userList as $user){
            $this->mailer->sendNotifyToWishList($user,$product);
            $countOfNotifiedUsers++;
        }
        $this->flashBag->add('success',"{$countOfNotifiedUsers} users notified about the change!");
    }


}