<?php

namespace ShopBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use ShopBundle\Entity\SoldProduct;

/**
 * soldProductRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SoldProductRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * @param EntityManagerInterface $em
     */

    public function __construct(EntityManagerInterface $em)
    {
        parent::__construct($em, new \Doctrine\ORM\Mapping\ClassMetadata(SoldProduct::class));
    }

    /**
     * @param SoldProduct $soldProduct
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function save(SoldProduct $soldProduct){
        $this->_em->persist($soldProduct);
        $this->_em->flush();
    }
}
