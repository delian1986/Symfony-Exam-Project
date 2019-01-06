<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Product
 *
 * @ORM\Table(name="products")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\ProductRepository")
 */
class Product
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
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string $slug
     *
     * @ORM\Column(nullable=false, type="string", unique=true)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

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

    /**
     * @var string
     * @ORM\Column(name="description", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="image", type="string", nullable=false)
     */
    private $image;

    /**
     * @var User
     *
     * Many products have one owner. This is the owning side.
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\User", inversedBy="myProducts")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
     */
    private $owner;

    /**
     * @var Category
     * @ORM\ManyToOne(targetEntity="ShopBundle\Entity\Category", inversedBy="products")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id", nullable=false)
     * @Assert\NotBlank()
     */
    private $category;

    /**
     * @var boolean
     * @ORM\Column("is_listed", type="boolean")
     */
    private $isListed;

    /**
     * @var OrdersProducts|ArrayCollection
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\OrdersProducts", mappedBy="product")
     */
    private $inOrders;

    /**
     * @var int
     * @ORM\Column("sold_times",type="integer",options={"default" : 0})
     */
    private $soldTimes;

    /**
     * @var Review[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Review", mappedBy="product")
     */
    private $reviews;

    /**
     * @var Promotion[]|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Promotion", inversedBy="products")
     * @ORM\JoinTable(name="product_promotions")
     * @ORM\OrderBy({"discount" = "DESC"})
     */
    private $promotions;

    public function __construct()
    {
        $this->createdAt = new \DateTime('now');
        $this->inOrders = new ArrayCollection();
        $this->reviews = new ArrayCollection();
        $this->promotions = new ArrayCollection();

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
     * Set name.
     *
     * @param string $name
     *
     * @return Product
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
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Product
     */
    public function setSlug(string $slug): Product
    {
        $this->slug = $slug;
        return $this;
    }

    /**
     * Set price.
     *
     * @param string $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price.
     *
     * @return string
     */
    public function getPrice()
    {
        if (!$this->hasActivePromotion()) {
            return $this->price;
        }

        $discount = $this->price * $this->getBiggestActivePromotion()->getDiscount() / 100;
        return $this->price - $discount;
    }

    /**
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->price;
    }

    /**
     * @return ArrayCollection|Promotion[]
     */
    public function getPromotions()
    {
        return $this->promotions;
    }

    /**
     * @return bool
     */
    public function hasActivePromotion()
    {
        if ($this->getBiggestActivePromotion()) {
            return true;
        }
        return false;
    }

    /**
     * @return null|Promotion
     */
    public function getBiggestActivePromotion()
    {
        return $this->promotions->filter(function (Promotion $promotion) {
            return $promotion->isActive();
        })->first();
    }

    /**
     * @param ArrayCollection|Promotion[] $promotions
     */
    public function addPromotions($promotions)
    {
        $this->promotions = $promotions;
    }

    /**
     * @param Promotion $promotion
     */
    public function removePromotion(Promotion $promotion)
    {
        $this->promotions->removeElement($promotion);
    }

    /**
     * Set quantity.
     *
     * @param int $quantity
     *
     * @return Product
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
     * @return User
     */
    public function getOwner(): User
    {
        return $this->owner;
    }

    /**
     * @param User $owner
     * @return Product
     */
    public function setOwner(User $owner): Product
    {
        $this->owner = $owner;
        return $this;
    }

    /**
     * @return Category
     */
    public function getCategory(): ?Category
    {
        return $this->category;
    }

    /**
     * @param Category $category
     * @return Product
     */
    public function setCategory(Category $category): Product
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
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
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return string
     */
    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param string $image
     * @return Product
     */
    public function setImage(string $image): Product
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return bool
     */
    public function isListed(): ?bool
    {
        return $this->isListed;
    }

    /**
     * @param bool $isListed
     */
    public function setIsListed(bool $isListed): void
    {
        $this->isListed = $isListed;
    }

    /**
     * @return ArrayCollection|OrdersProducts
     */
    public function getInOrders()
    {
        return $this->inOrders;
    }

    /**
     * @param ArrayCollection|OrdersProducts $inOrders
     */
    public function addInOrders($inOrders): void
    {
        $this->inOrders[] = $inOrders;
    }

    /**
     * @return int
     */
    public function getSoldTimes(): int
    {
        return $this->soldTimes;
    }

    /**
     * @param int $soldTimes
     * @return Product
     */
    public function setSoldTimes(int $soldTimes): Product
    {
        $this->soldTimes = $soldTimes;
        return $this;
    }

    /**
     * @return ArrayCollection|Review[]
     */
    public function getReviews()
    {
        return $this->reviews;
    }

    /**
     * @param ArrayCollection|Review[] $reviews
     * @return Product
     */
    public function setReviews($reviews): Product
    {
        $this->reviews = $reviews;
        return $this;
    }

    /**
     * @return int
     */
    public function getAverageRating(): int
    {
        if (count($this->getReviews()) > 0) {
            $sum = array_reduce($this->getReviews()->toArray(), function ($sum, Review $review) {
                $sum += $review->getRating();

                return $sum;
            });

            return floor($sum / count($this->getReviews()));
        }

        return 0;
    }

    /**
     * @return bool
     */
    public function isInStock(): bool
    {
        return $this->quantity > 0;
    }

}
