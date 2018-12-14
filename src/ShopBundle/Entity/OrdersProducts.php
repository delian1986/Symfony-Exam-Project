<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * OrdersProducts
 *
 * @ORM\Table(name="orders_products")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\OrderProductsRepository")
 */
class OrdersProducts
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
     * @var Order
     *
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Order", inversedBy="products",cascade={"persist"})
     * @ORM\JoinColumn(name="order_id", referencedColumnName="id")
     */
    private $order;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Product", inversedBy="inOrders")
     * @ORM\JoinColumn(name="product_id",referencedColumnName="id")
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     */
    private $quantity;


    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateAdded", type="datetime")
     */
    private $dateAdded;

    public function __construct()
    {
        $this->dateAdded=new \DateTime('now');
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
     * Set orders.
     *
     * @param string $orders
     *
     * @return OrdersProducts
     */
    public function setOrders($orders)
    {
        $this->order = $orders;

        return $this;
    }

    /**
     * Get orders.
     *
     * @return string
     */
    public function getOrders()
    {
        return $this->order;
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return OrdersProducts
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
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
     * Set dateAdded.
     *
     * @param \DateTime $dateAdded
     *
     * @return OrdersProducts
     */
    public function setDateAdded($dateAdded)
    {
        $this->dateAdded = $dateAdded;

        return $this;
    }

    /**
     * Get dateAdded.
     *
     * @return \DateTime
     */
    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $products
     * @return OrdersProducts
     */
    public function setProduct(Product $products): OrdersProducts
    {
        $this->product = $products;
        return $this;
    }

}
