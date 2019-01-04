<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * SoldProduct
 *
 * @ORM\Table(name="sold_products")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\SoldProductRepository")
 */
class SoldProduct
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
     * @var Product
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product")
     * @ORM\JoinColumn(name="product_id",referencedColumnName="id",nullable=false)
     */
    private $product;

    /**
     * @var User
     *
     * Many products have one owner. This is the owning side.
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User", inversedBy="myProducts")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id",nullable=false)
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="price", type="decimal", precision=10, scale=2)
     * @Assert\NotBlank()
     * @Assert\Range(min="0.01", max="900000")
     */
    private $price;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min="0", max="5000")
     */
    private $quantity;

    /**
     * @var \DateTime
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
    }


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
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return SoldProduct
     */
    public function setProduct(Product $product): SoldProduct
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return SoldProduct
     */
    public function setOwner(User $owner): SoldProduct
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return string
     */
    public function getPrice(): string
    {
        return $this->price;
    }

    /**
     * @param string $price
     * @return SoldProduct
     */
    public function setPrice(string $price): SoldProduct
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return SoldProduct
     */
    public function setQuantity(int $quantity): SoldProduct
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return SoldProduct
     */
    public function setCreatedAt(\DateTime $createdAt): SoldProduct
    {
        $this->createdAt = $createdAt;
        return $this;
    }

}
