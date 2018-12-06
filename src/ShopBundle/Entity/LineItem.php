<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
use Doctrine\ORM\Mapping\OneToOne;

/**
 * LineItem represents single product item
 *
 * @ORM\Table(name="line_items")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\LineItemRepository")
 */
class LineItem
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
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;

    /**
     * One LineItem hold One Product.
     * @var Product
     * @OneToOne(targetEntity="ShopBundle\Entity\Product")
     * @JoinColumn(name="product_id", referencedColumnName="id")
     */
    private $product;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Order", mappedBy="user")
     */
    private $userOrders;

    /**
     * @var ArrayCollection|Product[]
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\User", mappedBy="cart")
     * @ORM\JoinTable(name="users_carts")
     */
    private $userCart;

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
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
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return LineItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUserOrders(): ArrayCollection
    {
        return $this->userOrders;
    }

    /**
     * @param ArrayCollection $userOrders
     */
    public function setUserOrders(ArrayCollection $userOrders): void
    {
        $this->userOrders = $userOrders;
    }


    /**
     * Get quantity.
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getTotal(): float
    {
        return $this->getProduct()->getPrice() * $this->quantity;
    }
}
