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
use Symfony\Component\HttpFoundation\Request;

/**
 * Warehouse controller.
 *
 * @Route("/warehouse")
 */
class WarehouseController extends Controller{
    //put your code here
    
    /**
     * Action show the annotation for exercise solution.
     *
     * @Route("/annotations", name="exercise_annotations")
     * @Method("GET")
     * @Template()
     */
    public function annotationsAction(Request $request)
    {
        return $this->render("AppBundle:Warehouse:annotations.html.twig");
    }
}
