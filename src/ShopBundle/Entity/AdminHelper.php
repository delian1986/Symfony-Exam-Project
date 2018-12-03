<?php

namespace ShopBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AdminHelper
 *
 * @ORM\Table(name="admin_helper")
 * @ORM\Entity(repositoryClass="ShopBundle\Repository\AdminHelperRepository")
 */
class AdminHelper
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
     * @var float
     *
     * @ORM\Column(name="initial_cash", type="decimal", precision=10, scale=2)
     */
    private $initialCash;


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
     * Set initialCash.
     *
     * @param float $initialCash
     *
     * @return AdminHelper
     */
    public function setInitialCash(float $initialCash)
    {
        $this->initialCash = $initialCash;

        return $this;
    }

    /**
     * Get initialCash.
     *
     * @return string
     */
    public function getInitialCash()
    {
        return $this->initialCash;
    }


}
