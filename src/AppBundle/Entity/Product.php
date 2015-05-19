<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use AppBundle\Entity\ProductBin;
use AppBundle\Entity\ProductOrder;

/**
 * Product
 *
 * @ORM\Table()
 * @ORM\Entity
 * @ORM\Entity(repositoryClass="AppBundle\Entity\ProductRepository")
 */
class Product
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="stockLevel", type="integer")
     */
    private $stockLevel;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity="ProductBin", inversedBy="$products")
     * @ORM\JoinColumn(name="bin_id", referencedColumnName="id")
     **/
    private $bin;
    
    /**
     * @ORM\OneToMany(targetEntity="ProductOrder", mappedBy="product")
     **/
    private $productOrders;

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
     * Set name
     *
     * @param string $name
     * @return Product
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set stockLevel
     *
     * @param integer $stockLevel
     * @return Product
     */
    public function setStockLevel($stockLevel)
    {
        $this->stockLevel = $stockLevel;

        return $this;
    }

    /**
     * Get stockLevel
     *
     * @return integer 
     */
    public function getStockLevel()
    {
        return $this->stockLevel;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Product
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set bin
     *
     * @param string $bin
     * @return Product
     */
    public function setBin($bin)
    {
        $this->bin = $bin;

        return $this;
    }

    /**
     * Get bin
     *
     * @return string 
     */
    public function getBin()
    {
        return $this->bin;
    }

    /**
     * Set orders
     *
     * @param string $orders
     * @return Product
     */
    public function setOrders($orders)
    {
        $this->orders = $orders;

        return $this;
    }

    /**
     * Get orders
     *
     * @return string 
     */
    public function getOrders()
    {
        return $this->orders;
    }   
    
    public function __toString() {
        return $this->getName();
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->productOrders = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add productOrders
     *
     * @param \AppBundle\Entity\ProductOrder $productOrders
     * @return Product
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
