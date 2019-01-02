<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Order
 *
 * @ORM\Table(name="orders")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\OrderRepository")
 */
class Order
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
     * @var User $user
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User",inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var \DateTime $createdAt
     *
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @var ArrayCollection|OrdersProducts
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\OrdersProducts",mappedBy="order",cascade={"persist"})
     */
    private $products;

    /**
     * @var OrderStatus
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\OrderStatus",inversedBy="order")
     * @ORM\JoinColumn(name="status_id",referencedColumnName="id", nullable=false)
     */
    private $status;


    public function __construct()
    {
        $this->dateCreated = new \DateTime('now');
        $this->products = new ArrayCollection();
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
     * Set dateCreated.
     *
     * @param \DateTime $dateCreated
     *
     * @return Order
     */
    public function setDateCreated($dateCreated)
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    /**
     * Get dateCreated.
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user): void
    {
        $this->user = $user;
    }

    /**
     * @return ArrayCollection|OrdersProducts
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection|OrdersProducts $products
     */
    public function setProducts($products): void
    {
        $this->products[] = $products;
    }

    /**
     * @return OrderStatus
     */
    public function getStatus(): OrderStatus
    {
        return $this->status;
    }


    public function setStatus($status): void
    {
        $this->status = $status;
    }

    public function getTotal(): float
    {
        $productsInOrder = $this->getProducts();
        $sum = 0.00;
        /** @var OrdersProducts $product */
        foreach ($productsInOrder as $order) {
            $sum += $order->getProduct()->getPrice() * $order->getQuantity();
        }
        return $sum;
    }


}
