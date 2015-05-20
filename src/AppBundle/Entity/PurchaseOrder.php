<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Product;
use AppBundle\Entity\User;
use AppBundle\Entity\ProductOrder;

/**
 * ProductOrder
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks()
 */
class PurchaseOrder
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="orders")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     **/
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="ProductOrder", mappedBy="purchaseOrder")
     **/
    private $productOrders;


    public function __construct() {
        $this->products = new ArrayCollection();
        
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return ProductOrder
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set user
     *
     * @param string $user
     * @return ProductOrder
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @ORM\PrePersist
     */
    public function setCreatedAtValue()
    {
        $this->date = new \DateTime();
    }

    /**
     * Add productOrders
     *
     * @param \AppBundle\Entity\ProductOrder $productOrders
     * @return PurchaseOrder
     */
    public function addProductOrder(\AppBundle\Entity\ProductOrder $productOrders)
    {
        $this->productOrders[] = $productOrders;

        return $this;
    }

    /**
     * Remove productOrders
     *
     * @param \AppBundle\Entity\ProductOrder $productOrders
     */
    public function removeProductOrder(\AppBundle\Entity\ProductOrder $productOrders)
    {
        $this->productOrders->removeElement($productOrders);
    }

    /**
     * Get productOrders
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProductOrders()
    {
        return $this->productOrders;
    }
}
