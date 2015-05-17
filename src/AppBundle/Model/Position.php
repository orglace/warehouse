<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Model;

/**
 * Description of Position
 *
 * @author Luis
 */
class Position {
    //put your code here
    protected $x;
    protected $y;
    protected $distance;
    
    function __construct($x, $y, $distance) {
        $this->x = $x;
        $this->y = $y;
        $this->distance = $distance;
    }
    public function getX() {
        return $this->x;
    }

    public function getY() {
        return $this->y;
    }

    public function getDistance() {
        return $this->distance;
    }

    public function setX($x) {
        $this->x = $x;
    }

    public function setY($y) {
        $this->y = $y;
    }

    public function setDistance($distance) {
        $this->distance = $distance;
    }
}
