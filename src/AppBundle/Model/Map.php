<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model;

use AppBundle\Entity\ProductBin;

/**
 * Description of Map
 *
 * @author Luis
 */
class Map {
    //put your code here
    private $intWidth;
    private $intHeight;
    private $arrWarehouse;
    
    /*public function __construct($arrBinNames, $intBinAmount) {
        
        $intBinNamesCount = count($arrBinNames);
        $this->intWidth = $intBinNamesCount + floor($intBinNamesCount/2) + $intBinNamesCount%2 + 1;
        $this->intHeight = $intBinAmount + 2;        
        
        for ($x = 0; $x < $this->intHeight; $x++) {
            for ($y = 0; $y < $this->intWidth; $y++) 
                $this->arrWarehouse[$x][$y] = ' ';
        }
        
        for ($x = 1; $x < $this->intHeight - 1; $x++) {
            for ($i = 0, $y = 1; $i < $intBinNamesCount; $i+=2,$y+=3) {
                
                $this->arrWarehouse[$x][$y] = $arrBinNames[$i].$x;
                if (isset($arrBinNames[$i+1]))
                    $this->arrWarehouse[$x][$y+1] = $arrBinNames[$i+1].$x;
            }
        }
    }*/
    
    function __construct($intWidth, $intHeight, $arrProductBin) {
        $this->intWidth = $intWidth;
        $this->intHeight = $intHeight;

        for ($x = 0; $x < $this->intHeight; $x++) {
            for ($y = 0; $y < $this->intWidth; $y++) 
                $this->arrWarehouse[$x][$y] = ' ';
        }
        
        foreach ($arrProductBin as $objProductBin) {
            
            $objFromJson = json_decode($objProductBin->getLocation());
            $this->arrWarehouse[$objFromJson->{'x'}][$objFromJson->{'y'}] = $objProductBin->getName();
        }
    }

 
    public function getIntWidth() {
        return $this->intWidth;
    }

    public function getIntHeight() {
        return $this->intHeight;
    }

    public function getArrWarehouse() {
        return $this->arrWarehouse;
    }
}
