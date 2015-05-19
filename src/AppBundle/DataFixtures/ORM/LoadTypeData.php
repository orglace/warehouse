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
use AppBundle\Entity\Type;

/**
 * Description of LoadTypeData
 *
 * @author Luis
 */
class LoadTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager) {
        
        for ($i = 1; $i <= 60; $i++) {
            $objType = new Type();
            $objType->setName("Type ".$i);
            
            $manager->persist($objType);
            $arrType[] = $objType;
        }
        $manager->flush();
    }
    
    public function getOrder() {
        return 2;
    }
}
