<?php


namespace ShopBundle\Service;


use ShopBundle\Entity\InitialCash;
use ShopBundle\Repository\InitialCashRepository;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;

class InitialCashService implements InitialCashServiceInterface
{
    /**
     * @var InitialCashRepository
     */
    private $initialCashRepository;

    /**
     * @var FlashBagInterface
     */
    private $flashBag;

    public function __construct(InitialCashRepository $initialCashRepository,FlashBagInterface $flashBag)
    {
        $this->initialCashRepository=$initialCashRepository;
        $this->flashBag=$flashBag;
    }

    /**
     * @return float
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getInitialCashValue():float
    {
        return $this->initialCashRepository->getInitialCashValue();
    }

    public function getRecordsCount(): int
    {
        return $this->initialCashRepository->getInitialCashRecordsCount();
    }

    /**
     * @param InitialCash $initialCash
     * @throws \Doctrine\ORM\ORMException
     */
    public function remove(InitialCash $initialCash): void
    {
        $this->initialCashRepository->delete($initialCash);
    }

    /**
     * @param InitialCash $initialCash
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function insertInitialCash(InitialCash $initialCash): void
    {
        $initialCashRecords = $this->initialCashRepository->getInitialCashRecordsCount();
        if (null!==$initialCashRecords) {

            //remove old initial cash value
            foreach ($initialCashRecords as $row){
                $this->initialCashRepository->delete($row);
            }
        }
        //set new initial cash value
        $this->initialCashRepository->save($initialCash);
        $this->flashBag->add('success',"Initial cash value of {$initialCash->getInitialCash()} for new registered users was set!");
    }

    public function getOldInitialCashValue(): ?InitialCash
    {
        return $this->initialCashRepository->getOldInitialCash();
    }
}