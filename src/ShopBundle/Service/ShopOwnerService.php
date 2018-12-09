<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\ShopOwner;
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

    public function __construct(ShopOwnerRepository $shopOwnerRepository, FlashBagInterface $flashBag)
    {
        $this->flashBag = $flashBag;
        $this->shopOwnerRepository = $shopOwnerRepository;
    }

    /**
     * @param ShopOwner $shopOwner
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function setShopOwner(ShopOwner $shopOwner): void
    {
        /** @var ShopOwner $currentOwner */
        $currentOwner = $this->shopOwnerRepository->getShopOwner();

        //if there is shop owner
        if (null !== $currentOwner) {
            $ownerId = $currentOwner->getShopOwner()->getId();
            $newOwnerId = $shopOwner->getShopOwner()->getId();
            if ($ownerId === $newOwnerId) {
                $this->flashBag->add('danger', "{$shopOwner->getShopOwner()->getEmail()} already owns the shop!");
            } elseif ($ownerId !== $newOwnerId) {
                $this->shopOwnerRepository->removeOwner($currentOwner);
                $this->flashBag->add('danger', "{$currentOwner->getShopOwner()->getEmail()} no longer owns the shop!");
                $this->flashBag->add('success', "Shop Owner set to {$shopOwner->getShopOwner()->getEmail()} !");
                $this->shopOwnerRepository->setOwner($shopOwner);
            }
        } else {
            $this->flashBag->add('success', "Shop Owner set to {$shopOwner->getShopOwner()->getEmail()} !");
            $this->shopOwnerRepository->setOwner($shopOwner);
        }
    }
}