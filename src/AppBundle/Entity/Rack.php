<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Rack
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Rack
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
     * @ORM\Column(name="name", type="string", length=50)
     */
    private $name;

    /**
     * @var array
     *
     * @ORM\Column(name="packingStation", type="json_array")
     */
    private $packingStation;

    /**
     * @ORM\OneToMany(targetEntity="ProductBin", mappedBy="rack")
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
     * @return Rack
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
     * Set packingStation
     *
     * @param array $packingStation
     * @return Rack
     */
    public function setPackingStation($packingStation)
    {
        $this->packingStation = $packingStation;

        return $this;
    }

    /**
     * Get packingStation
     *
     * @return array 
     */
    public function getPackingStation()
    {
        return $this->packingStation;
    }

    /**
     * Set productBins
     *
     * @param string $productBins
     * @return Rack
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
     * @return Rack
     */
    public function addProductBin(\AppBundle\Entity\ProductBin $productBins)
    {
        $this->productBins[] = $productBins;

        return $this;
    }

    /**
     * Remove productBins
     *
     * @param \AppBundle\Entity\ProductBin $productBins
     */
    public function removeProductBin(\AppBundle\Entity\ProductBin $productBins)
    {
        $this->productBins->removeElement($productBins);
    }
}
