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
use AppBundle\Entity\Rack;

/**
 * Description of LoadRackData
 *
 * @author Luis
 */
class LoadRackData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager) {
        
        for ($i = 1, $j = 1; $i <= 3; $i++, $j+=3) {
            $objRack = new Rack();
            $objRack->setName("Rack ".$i);
            $objRack->setPackingStation(json_encode(array('x' => 0, 'y' => $j, 'name' => "Packing Station ".$i)));
            
            $manager->persist($objRack);
            $arrRack[] = $objRack;
        }
        $manager->flush();
    }
    
    public function getOrder() {
        return 3;
    }

//put your code here
}
