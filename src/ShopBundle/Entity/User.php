<?php

namespace ShopBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use function Symfony\Component\Debug\Tests\testHeader;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="users")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\UserRepository")
 */
class User implements UserInterface, \Serializable
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
     */
    private $moneySpent;

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
    private $roles;

    /**
     * @var ArrayCollection|Product
     * One user has many products. This is the inverse side.
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Product", mappedBy="owner", cascade={"remove"})
     */
    private $myProducts;

    /**
     * @var ArrayCollection|Product[]
     *
     * @ORM\ManyToMany(targetEntity="ShopBundle\Entity\Product", inversedBy="userCart")
     * @ORM\JoinTable(name="users_carts")
     */
    private $cart;

    /**
     * @var ArrayCollection|Order
     * @ORM\OneToMany(targetEntity="ShopBundle\Entity\Order", mappedBy="user")
     */
    private $orders;

    public function __construct()
    {
        $this->roles = new ArrayCollection();
        $this->myProducts=new ArrayCollection();
        $this->cart=new ArrayCollection();
        $this->orders=new ArrayCollection();
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
     * @return ArrayCollection|Product
     */
    public function getMyProducts()
    {
        return $this->myProducts;
    }

    /**
     * @param ArrayCollection|Product $myProducts
     * @return User
     */
    public function setMyProducts(Product $myProducts): User
    {
        $this->myProducts[]= $myProducts;
        return $this;
    }

    /**
     * @return ArrayCollection|Product
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @param ArrayCollection|Product $cart
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
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


    /**
     * @return bool
     */
    public function isAdmin()
    {
        return in_array('ROLE_ADMIN', $this->getRoles());
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
        foreach ($this->roles as $role) {
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
        $this->roles[] = $role;

        return $this;
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
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }


    public function serialize()
    {
        return serialize([
            $this->getId(),
            $this->getUsername(),
            $this->getPassword()
        ]);
    }


    public function unserialize($serialized)
    {
        list(
            $this->id,
            $this->email,
            $this->password
            ) = unserialize($serialized);
    }
}

