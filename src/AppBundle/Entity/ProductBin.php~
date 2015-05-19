<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\Product;
use AppBundle\Entity\Type;
use AppBundle\Entity\Rack;

/**
 * ProductBin
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class ProductBin
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
     * @var array
     *
     * @ORM\Column(name="location", type="json_array")
     */
    private $location;

    /**
     * @ORM\ManyToOne(targetEntity="Type", inversedBy="productBins")
     * @ORM\JoinColumn(name="type_id", referencedColumnName="id")
     **/
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Rack", inversedBy="productBins")
     * @ORM\JoinColumn(name="rack_id", referencedColumnName="id")
     **/
    private $rack;
    
    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="bin")
     **/
    private $products;

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
     * Set name
     *
     * @param string $name
     * @return ProductBin
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
     * Set location
     *
     * @param array $location
     * @return ProductBin
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return array 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return ProductBin
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set rack
     *
     * @param string $rack
     * @return ProductBin
     */
    public function setRack($rack)
    {
        $this->rack = $rack;

        return $this;
    }

    /**
     * Get rack
     *
     * @return string 
     */
    public function getRack()
    {
        return $this->rack;
    }

    /**
     * Add products
     *
     * @param \AppBundle\Entity\Product $products
     * @return ProductBin
     */
    public function addProduct(Product $products)
    {
        $this->products[] = $products;

        return $this;
    }

    /**
     * Remove products
     *
     * @param \AppBundle\Entity\Product $products
     */
    public function removeProduct(Product $products)
    {
        $this->products->removeElement($products);
    }

    /**
     * Get products
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getProducts()
    {
        return $this->products;
    }
}
