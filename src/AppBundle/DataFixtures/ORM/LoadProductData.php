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
use AppBundle\Entity\Product;

/**
 * Description of LoadProductData
 *
 * @author Luis
 */
class LoadProductData extends AbstractFixture implements OrderedFixtureInterface
{
    public function load(ObjectManager $manager) 
    {
        $arrProductBin = $manager->getRepository('AppBundle:ProductBin')->findAll();
        $intLength = count($arrProductBin);
        
        for ($i = 1; $i <= $intLength; $i++) {
            
            $objProduct = new Product();
            $objProduct->setName("Product ".$i);
            $objProduct->setDescription("Product description ".$i);
            $objProduct->setStockLevel(rand(0, 100));
            $objProduct->setBin($arrProductBin[$i-1]);
            
            $manager->persist($objProduct);
        }
        $manager->flush();
    }

    public function getOrder() 
    {
        return 5;
    }

//put your code here
}
