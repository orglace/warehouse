<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use AppBundle\Entity\ProductBin;

/**
 * Type
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Type
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
     * @ORM\OneToMany(targetEntity="ProductBin", mappedBy="type")
     **/
    private $productBins;

    public function __construct() {
        $this->productBins = new ArrayCollection();
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
     * @return Type
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
     * Set productBins
     *
     * @param string $productBins
     * @return Type
     */
    public function setProductBins($productBins)
    {
        $this->productBins = $productBins;

        return $this;
    }

    /**
     * Get productBins
     *
     * @return string 
     */
    public function getProductBins()
    {
        return $this->productBins;
    }

    /**
     * Add productBins
     *
     * @param \AppBundle\Entity\ProductBin $productBins
     * @return Type
     */
    public function addProductBin(ProductBin $productBins)
    {
        $this->productBins[] = $productBins;

        return $this;
    }

    /**
     * Remove productBins
     *
     * @param \AppBundle\Entity\ProductBin $productBins
     */
    public function removeProductBin(ProductBin $productBins)
    {
        $this->productBins->removeElement($productBins);
    }
}
