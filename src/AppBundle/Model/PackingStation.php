<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model;
use AppBundle\Model\Position;

/**
 * Description of PackingStation
 *
 * @author Luis
 */
class PackingStation {
    //put your code here
    private $objPosition;
    private $strName;
    
    function __construct(Position $objPosition, $strName) {
        $this->$objPosition = $objPosition;
        $this->$strName = $strName;
    }
    function getObjPosition() {
        return $this->objPosition;
    }

    function getStrName() {
        return $this->strName;
    }

    function setObjPosition($objPosition) {
        $this->objPosition = $objPosition;
    }

    function setStrName($strName) {
        $this->strName = $strName;
    }


}
