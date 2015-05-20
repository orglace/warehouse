<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model;

/**
 * Description of Cell
 *
 * @author Luis
 */
class Cell {
    //put your code here
    public $strText;
    public $strClass;
    
    function __construct($strText, $strClass) {
        $this->strText = $strText;
        $this->strClass = $strClass;
    }
    
    public function getStrText() {
        return $this->strText;
    }

    public function getStrClass() {
        return $this->strClass;
    }

}
