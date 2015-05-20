<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * Description of ProductBinController
 *
 * @author Luis
 */

/**
 * PurchaseOrder controller.
 *
 * @Route("/bin")
 */
class ProductBinController extends Controller {
    //put your code here
    
    /**
     * Lists all ProductOrder entities.
     *
     * @Route("/{name}", name="bin_details")
     * @Method("GET")
     * @Template()
     */
    public function detailsAction($name)
    {
        $objEM = $this->getDoctrine()->getManager();
                
        $objProductBin = $objEM->getRepository('AppBundle:ProductBin')->findOneByName($name);
        if (!$objProductBin) {
            throw $this->createNotFoundException('Unable to find ProductBin entity.');
        }
        $arrProduct = $objProductBin->getProducts();
                
        return array(
            'objProductBin' => $objProductBin,
            'arrProduct' => $arrProduct
        );
    }
}
