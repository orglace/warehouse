<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\ProductBin;

/**
 * Description of LoadProductBinData
 *
 * @author Luis
 */
class LoadProductBinData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $arrBinNames = array("A", "B", "C", "D", "E", "F");
        $arrType = $manager->getRepository('AppBundle:Type')->findAll();
        $arrRack = $manager->getRepository('AppBundle:Rack')->findAll();
        
        $intBinAmount = 10;
        $intBinNamesCount = count($arrBinNames);
        $this->intWidth = $intBinNamesCount + floor($intBinNamesCount/2) + $intBinNamesCount%2 + 1;
        $this->intHeight = $intBinAmount + 2;        
        
        for ($x = 1; $x < $this->intHeight - 1; $x++) {
            for ($i = 0, $y = 1, $j = 0; $i < $intBinNamesCount; $i+=2, $y+=3, $j++) {
                
                $objType1 = array_shift($arrType);
                $objRack = $arrRack[$j];
                //$this->arrWarehouse[$x][$y] = $arrBinNames[$i].$x;
                $objProductBin1 = new ProductBin();
                $objProductBin1->setName($arrBinNames[$i].$x);
                $objProductBin1->setLocation(json_encode(array('x' => $x, 'y' => $y)));
                $objProductBin1->setType($objType1);
                $objProductBin1->setRack($objRack);
                
                $manager->persist($objProductBin1);
                
                $arrProductBin[] = $objProductBin1;
                
                if (isset($arrBinNames[$i+1])) {
                    //$this->arrWarehouse[$x][$y+1] = $arrBinNames[$i+1].$x;
                    $objType2 = array_shift($arrType);
                    $objProductBin2 = new ProductBin();
                    $objProductBin2->setName($arrBinNames[$i+1].$x);
                    $objProductBin2->setLocation(json_encode(array('x' => $x, 'y' => $y+1)));
                    $objProductBin2->setType($objType2);
                    $objProductBin2->setRack($objRack);

                    $manager->persist($objProductBin2);
                    
                    $arrProductBin[] = $objProductBin1;
                }
            }
        }
        $manager->flush();
    }

    public function getOrder() 
    {
        return 4;
    }

//put your code here
}
