<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model;

use AppBundle\Model\Path;

/**
 * Description of Route
 *
 * @author Luis
 */
class Route {
    //put your code here
    private $objMap;
    private $arrPath;
    private $intDistance;
    
    public function __construct($objMap, $arrPath = array()) {
        $this->arrPath = $arrPath;
    }

    public function addPath(Path $objPath) 
    {
        array_push($this->arrPath, $objPath);
    }
    
    public function getDistance() 
    {
        if(!isset($this->intDistance)){
            foreach ($this->arrPath as $objPath) {
                $this->intDistance += $objPath->getIntDistance();
            } 
        }
        return $this->intDistance;
    }
    
    public function getArrPath() {
        return $this->arrPath;
    }
}
