<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Promotion
 *
 * @ORM\Table(name="promotions")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\PromotionRepository")
 */
class Promotion
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
     * @var string
     * @Assert\NotBlank()
     * @Assert\Length(min="3", max="100")
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="discount", type="integer")
     * @Assert\NotBlank()
     * @Assert\Range(min="1", max="99")
     */
    private $discount;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="startDate", type="datetime")
     * @Assert\GreaterThan("-1 day")
     * @Assert\Date()
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="endDate", type="datetime")
     * @Assert\Expression(
     *     "this.getEndDate() > this.getStartDate()",
     *     message="End date should be greater than start date!"
     * )
     * @Assert\NotBlank()
     * @Assert\GreaterThan("now")
     * @Assert\Date()
     */
    private $endDate;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Product", mappedBy="promotions")
     */
    private $products;


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
     * Set name.
     *
     * @param string $name
     *
     * @return Promotion
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set discount.
     *
     * @param int $discount
     *
     * @return Promotion
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;

        return $this;
    }

    /**
     * Get discount.
     *
     * @return int
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Set startDate.
     *
     * @param \DateTime $startDate
     *
     * @return Promotion
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate.
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate.
     *
     * @param \DateTime $endDate
     *
     * @return Promotion
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate.
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return ArrayCollection
     */
    public function getProducts(): ArrayCollection
    {
        return $this->products;
    }

    /**
     * @param ArrayCollection $products
     */
    public function setProducts(ArrayCollection $products): void
    {
        $this->products = $products;
    }

    /**
     * @return  bool
     * @throws \Exception
     */
    public function isActive()
    {
        if ($this->getStartDate() < new \DateTime('now') &&
            $this->getEndDate() > new \DateTime('now')) {
            return true;
        }

        return false;
    }

    public function __toString()
    {
        return $this->getName() . " / {$this->getDiscount()} % discount on products";
    }
}
