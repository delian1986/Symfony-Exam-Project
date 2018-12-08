<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ShopOwner
 *
 * @ORM\Table(name="shop_owner")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\ShopOwnerRepository")
 */
class ShopOwner
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     * @ORM\OneToOne(targetEntity="ShopBundle\Entity\User")
     * @ORM\JoinColumn(name="owner_id",referencedColumnName="id",nullable=false)
     */
    private $shopOwner;


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getShopOwner(): ?User
    {
        return $this->shopOwner;
    }

    /**
     * @param User $shopOwner
     */
    public function setShopOwner(User $shopOwner): void
    {
        $this->shopOwner = $shopOwner;
    }
}
