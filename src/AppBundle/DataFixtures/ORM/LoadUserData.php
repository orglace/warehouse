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
use AppBundle\Entity\User;
/**
 * Description of LoadUserData
 *
 * @author Luis
 */
class LoadUserData extends AbstractFixture implements OrderedFixtureInterface
{
    //put your code here
    public function load(ObjectManager $manager) 
    {
        $objUser = new User();
        $objUser->setFullName("User Full Name");
        $objUser->setUsername("admin");
        $objUser->setPassword("adminpass");
        $objUser->setEmail("admin@warehouse.com");
        $objUser->setIsActive(true);
        
        $manager->persist($objUser);
        $manager->flush();
        
        $this->addReference('admin-user', $objUser);
    }

    public function getOrder() 
    {
        return 1;
    }

}
