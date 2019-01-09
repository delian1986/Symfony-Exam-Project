<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Product;
use ShopBundle\Entity\ShopOwner;
use ShopBundle\Entity\User;
use ShopBundle\Repository\ProductRepository;
use ShopBundle\Repository\RoleRepository;
use ShopBundle\Repository\ShopOwnerRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class ShopOwnerService implements ShopOwnerServiceInterface
{
    /**
     * @var ShopOwnerRepository
     */
    private $shopOwnerRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var MailerInterface
     */
    private $mailer;

    /**
     * @var RoleServiceInterface
     */
    private $roleService;

    public function __construct(ShopOwnerRepository $shopOwnerRepository,
                                FlashBagInterface $flashBag,
                                ProductRepository $productRepository,
                                MailerInterface $mailer,
                                RoleServiceInterface $roleService)
    {
        $this->flashBag = $flashBag;
        $this->shopOwnerRepository = $shopOwnerRepository;
        $this->productRepository = $productRepository;
        $this->mailer = $mailer;
        $this->roleService = $roleService;
    }

    /**
     * @param ShopOwner $shopOwner
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function changeShopOwner(ShopOwner $shopOwner): void
    {
        /** @var ShopOwner $currentOwner */
        $currentOwner = $this->shopOwnerRepository->getShopOwner();

        //if there is shop owner
        if (null !== $currentOwner) {
            $oldOwnerId = $currentOwner->getShopOwner()->getId();
            $newOwnerId = $shopOwner->getShopOwner()->getId();

            if ($oldOwnerId === $newOwnerId) {
                $this->flashBag->add('danger', "{$shopOwner->getShopOwner()->getEmail()} already owns the shop!");
            } else {

                $currentOwnerProducts = $this->productRepository->findBy(['owner' => $currentOwner->getShopOwner()]);
                $this->shopOwnerRepository->removeOwner($currentOwner);

                $newOwner = $shopOwner->getShopOwner();
                if (!$newOwner->isAdmin()) {
                    $this->roleService->setRoleByName(['name' => 'ROLE_ADMIN'], $newOwner);
                }

                if (null !== $currentOwnerProducts) {
                    $this->changeProductsToNewShopOwner($currentOwnerProducts, $shopOwner->getShopOwner());
                }

                $this->flashBag->add('danger', "{$currentOwner->getShopOwner()->getEmail()} no longer owns the shop!");
                $this->flashBag->add('success', "Shop Owner set to {$shopOwner->getShopOwner()->getEmail()} !");
                $this->shopOwnerRepository->setOwner($shopOwner);
                $this->mailer->sendShopOwnerNotice($currentOwner->getShopOwner(), $shopOwner->getShopOwner());
            }
        }
    }

    /**
     * @param $products
     * @param User $newOwner
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    private function changeProductsToNewShopOwner($products, User $newOwner)
    {
        /** @var Product[] $products */
        foreach ($products as $product) {
            $product->setOwner($newOwner);
            $this->productRepository->save($product);
        }
    }

    /**
     * @param ShopOwner $shopOwner
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function saveShopOwner(ShopOwner $shopOwner): void
    {
        $this->shopOwnerRepository->setOwner($shopOwner);
    }

    public function getOwner(): User
    {
        $shopOwner = $this->shopOwnerRepository->getShopOwner();
        return $shopOwner->getShopOwner();
    }


}