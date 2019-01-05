<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\Promotion;
use ShopBundle\Repository\PromotionRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class PromotionService implements PromotionServiceInterface
{
    /**
     * @var PromotionRepository
     */
    private $promotionRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(PromotionRepository $promotionRepository,
                                FlashBagInterface $flashBag)
    {
        $this->promotionRepository=$promotionRepository;
        $this->flashBag=$flashBag;
    }

    /**
     * @param Promotion $promotion
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function addPromotion(Promotion $promotion): void
    {
        $this->promotionRepository->save($promotion);
        $this->flashBag->add('success', "Promotion {$promotion->getName()} saved!");
    }
}