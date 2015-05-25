<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * PurchaseOrder controller.
 *
 * @Route("/product")
 */
class ProductController extends Controller{
    //put your code here
    
    /**
     * Action that get the last five sold products.
     *
     * @Route("/5moresold", name="product_five_more_sold")
     * @Method("GET")
     * @Template()
     */
    public function fiveMoreSoldAction() {
       
        $objEM = $this->getDoctrine()->getManager();
        
        
        $query = $objEM->createQuery(
            "SELECT p.name as name, sum(po.quantity) AS quantity, p.stockLevel 
             FROM AppBundle:ProductOrder po JOIN po.product p 
             GROUP BY p.id 
             ORDER BY quantity DESC"
        )->setMaxResults(5);

        $arrProduct = $objEM->getRepository('AppBundle:Product')->fiveMoreSold();
        
        return array(
            'arrProduct' => $arrProduct,
        );
    }
}
