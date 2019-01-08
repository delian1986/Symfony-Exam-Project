<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\Security\Core\User\AdvancedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\UserRepository")
 */
class User implements AdvancedUserInterface, \Serializable
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
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email(message = "The email '{{ value }}' is not a valid email.")
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="full_name", type="string", length=255)
     * @Assert\NotBlank()
     */
    private $fullName;

    /**
     * @var float
     * @ORM\Column(name="balance", type="decimal", precision=10, scale=2)
     */
    private $balance;

    /**
     * @var float
     * @ORM\Column(name="money_spent", type="decimal", precision=10, scale=2)
     * @Assert\Range(min="0", max="100000")
     */
    private $moneySpent;

    /**
     * @var bool
     * @ORM\Column(name="is_active", type="boolean", options={"default":1})
     */
    private $isActive;

    /**
     * @var float
     * @ORM\Column(name="money_received", type="decimal", precision=10, scale=2)
     */
    private $moneyReceived;

    /**
     * @var Role|ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Role", inversedBy="users")
     * @ORM\JoinTable(name="users_roles")
     */
    private $sroles;

    /**
     * @var ArrayCollection|Product
     * One user has many products. This is the inverse side.
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\SoldProduct", mappedBy="owner", cascade={"remove"})
     */
    private $myProducts;

    /**
     * @var ArrayCollection|Order
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Order", mappedBy="user")
     * @ORM\OrderBy({"dateCreated":"desc"})
     */
    private $orders;

    /**
     * @var Review[]|ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Review", mappedBy="author")
     */
    private $reviews;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->myProducts = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->reviews = new ArrayCollection();

    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set fullName
     *
     * @param string $fullName
     *
     * @return User
     */
    public function setFullName($fullName)
    {
        $this->fullName = $fullName;

        return $this;
    }


    /**
     * Get fullName
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @return array
     */
    public function getMyProducts()
    {
        $boughProducts=[];

        foreach ($this->myProducts as $product) {
            if (!isset($products[$product->getProduct()->getName()])){
                $boughProducts[$product->getProduct()->getName()][]=$product;
                continue;
            }

            $boughProducts[$product->getProduct()->getName()][]=$product;
        }
        return $boughProducts;
    }

    public function getListOfBoughtProducts(){
        $products=[];
        foreach ($this->myProducts as $deal){
            $products[$deal->getProduct()->getId()]=$deal->getProduct();
        }
        return $products;
    }

    /**
     * @param ArrayCollection|Product $myProducts
     * @return User
     */
    public function setMyProducts(Product $myProducts): User
    {
        $this->myProducts[] = $myProducts;
        return $this;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     */
    public function setBalance(float $balance): void
    {
        $this->balance = $balance;
    }

    /**
     * @return ArrayCollection|Order
     */
    public function getOrders()
    {
        return $this->orders;
    }

    /**
     * @param ArrayCollection|Order $orders
     */
    public function setOrders($orders): void
    {
        $this->orders = $orders;
    }

    /**
     * @return float
     */
    public function getMoneySpent(): float
    {
        return $this->moneySpent;
    }

    /**
     * @param float $moneySpent
     */
    public function setMoneySpent(float $moneySpent): void
    {
        $this->moneySpent = $moneySpent;
    }

    /**
     * @return float
     */
    public function getMoneyReceived(): float
    {
        return $this->moneyReceived;
    }

    /**
     * @param float $moneyReceived
     */
    public function setMoneyReceived(float $moneyReceived): void
    {
        $this->moneyReceived = $moneyReceived;
    }

    public function itemsInCart(){
        foreach ($this->getOrders() as $order){
           if($order->getStatus()->getName() === 'Open'){
                $itemsInCart=0;
                foreach ($order->getProducts() as $product){
                    $itemsInCart+=$product->getQuantity();
                }
                return $itemsInCart;
           }
        }
        return 0;
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
     * @return User
     */
    public function setReviews($reviews): User
    {
        $this->reviews = $reviews;
        return $this;
    }



    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
    }

    /**
     * @return bool
     */
    public function isEditor()
    {
        return in_array('ROLE_EDITOR', $this->getRoles());
    }
    /**
     * Returns the roles granted to the user.
     *
     *     public function getRoles()
     *     {
     *         return array('ROLE_USER');
     *     }
     *
     * Alternatively, the roles might be stored on a ``roles`` property,
     * and populated in any number of different ways when the user object
     * is created.
     *
     * @return array (Role|string)[] The user roles
     */
    public function getRoles()
    {
        $stringRoles = [];
        foreach ($this->sroles as $role) {
            /** @var $role Role */
            $stringRoles[] = $role->getRole();
        }

        return $stringRoles;
    }



    /**
     * @param \ShopBundle\Entity\Role $role
     * @return User
     */
    public function addRole(Role $role)
    {
        $this->sroles[] = $role;

        return $this;
    }

    function getSroles()
    {
        return $this->sroles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *

     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the username used to authenticate the user.
     *
     * @return string The username
     */
    public function getUsername()
    {
        return $this->email;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     * @return User
     */
    public function setIsActive(bool $isActive): User
    {
        $this->isActive=$isActive;
        return $this;
    }



    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        return true;
    }

    public function isAccountNonExpired()
    {
        return true;
    }

    public function isAccountNonLocked()
    {
        return true;
    }

    public function isCredentialsNonExpired()
    {
        return true;
    }

    public function isEnabled()
    {
        return $this->isActive;
    }


    public function serialize()
    {
        return serialize([
            $this->getId(),
            $this->getUsername(),
            $this->getPassword(),
            $this->isActive
        ]);
    }


    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password,
            $this->isActive
            ) = unserialize($serialized);
    }
}

