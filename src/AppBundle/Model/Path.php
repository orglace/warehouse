<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model;

use AppBundle\Model\Map;
use AppBundle\Model\Position;

/**
 * Description of Road
 *
 * @author Luis
 */
class Path {
    //put your code here
    private $objCurrentPosition;
    private $strBinName;
    private $intDistance;
    private $arrPosition;
    private $arrPath;
    
    public function __construct($objMap, $objCurrentPosition, $strBinName) {
        $this->objCurrentPosition = $objCurrentPosition;
        $this->strBinName = $strBinName;
        $this->arrPosition = array();
        $this->intDistance = $this->getIntDistance($objMap);
    }

    public function getObjCurrentPosition() {
        return $this->objCurrentPosition;
    }

    public function getStrBinName() {
        return $this->strBinName;
    }

    public function getIntDistance(Map $objMap = null) {
        
        if ($objMap != null) {
            $arrWarehouse = $objMap->getArrWarehouse();
            $intWidth = $objMap->getIntWidth();
            $intHeight = $objMap->getIntHeight();
            $arrXScroll = array(0, 0, 1, -1);
            $arrYScroll = array(1, -1, 0, 0);
            $arrVisited;
            $quePositions[0] = $this->objCurrentPosition;
            $this->arrPath[$this->objCurrentPosition->getX()][$this->objCurrentPosition->getY()] = new Position(-1, -1, -1);

            while (count($quePositions) != 0) {
                $objCurrent = array_shift($quePositions);
                $x = $objCurrent->getX();
                $y = $objCurrent->getY();
                if((isset($arrWarehouse[$x][$y-1]) && $arrWarehouse[$x][$y-1] == $this->strBinName) || (isset($arrWarehouse[$x][$y+1]) && $arrWarehouse[$x][$y+1] == $this->strBinName)) {
                    $this->setArrPosition($objCurrent);
                    $this->intDistance = $objCurrent->getDistance();
                    return $this->intDistance;
                }
                $arrVisited[$x][$y] = true;

                for ($i = 0; $i < 4; $i++) {
                    $nx = $arrXScroll[$i] + $x;
                    $ny = $arrYScroll[$i] + $y;

                    if($nx >= 0 && $nx < $intHeight && $ny >= 0 && $ny < $intWidth && $arrWarehouse[$nx][$ny] == " " && !isset($arrVisited[$nx][$ny])) {
                        array_push($quePositions, new Position($nx, $ny, $objCurrent->getDistance() + 1));
                        $this->arrPath[$nx][$ny] = $objCurrent;                   
                    }
                }
            }
            $this->intDistance = -1;
        }
        
        return $this->intDistance;
    }

    public function getArrPosition() {
        return $this->arrPosition;
    }
    
    public function getLastPosition() {
        $arrValues = array_values($this->getArrPosition());
        return end($arrValues);
    }

    private function setArrPosition($objPosition) {
        
        if(-1 != $objPosition->getDistance()) {
            array_unshift($this->arrPosition, $objPosition);
            $this->setArrPosition($this->arrPath[$objPosition->getX()][$objPosition->getY()]);
        }
    }
    
    public function setReverseArrPosition() {
        
        $this->arrPosition = array_reverse($this->arrPosition);
    }
}
