<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * Description of ProductRepository
 *
 * @author Luis
 */
class ProductRepository extends EntityRepository
{
    public function findAllById($arrId) 
    {
        $repository = $this->getEntityManager()->getRepository('AppBundle:Product');

        $query = $repository->createQueryBuilder('p')
            ->where('p.id IN(:ids)')
            ->setParameter('ids', $arrId)
            ->getQuery();

        $products = $query->getResult();
        return $products;
    }
    //put your code here
    
    public function fiveMoreSold() {
        
        $objEM = $this->getEntityManager();
        
        $query = $objEM->createQuery(
            "SELECT p.name as name, sum(po.quantity) AS quantity, p.stockLevel 
             FROM AppBundle:ProductOrder po JOIN po.product p 
             GROUP BY p.id 
             ORDER BY quantity DESC"
        )->setMaxResults(5);

        return $query->getResult();
    }
}
