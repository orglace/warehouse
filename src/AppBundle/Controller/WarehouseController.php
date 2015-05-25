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

use AppBundle\Model\Position;

/**
 * Warehouse controller.
 *
 * @Route("/warehouse")
 */
class WarehouseController extends Controller{
    //put your code here
    
    /**
     * Action that show the Warehouse Map.
     *
     * @Route("/map", name="warehouse_map")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $objWarehouseService = $this->get("app_warehouse.map");
        
        $objEM = $this->getDoctrine()->getManager();
        $objOriginalMap = $objWarehouseService->createMap();
        $arrWarehouse = $objOriginalMap->getArrWarehouse();
        
        $arrRack = $objEM->getRepository('AppBundle:Rack')->findAll();
        
        $arrWarehouse = $objWarehouseService->updateMap($objOriginalMap, null, $arrRack);
        
        return array(
            'arrWarehouse' => $arrWarehouse,
        );
    }
    
    /**
     * Action that draw an Order route.
     *
     * @Route("/map/{id}/route", name="warehouse_map_route")
     * @Method("GET")
     * @Template()
     */
    public function routeAction($id)
    {
        $objWarehouseService = $this->get("app_warehouse.map");
        
        $objEM = $this->getDoctrine()->getManager();
        $objOriginalMap = $objWarehouseService->createMap();
        //$arrWarehouse = $objOriginalMap->getArrWarehouse();
        $arrPurchaseOrder = $objEM->getRepository('AppBundle:PurchaseOrder')->find($id);
        $arrProductOrder = $arrPurchaseOrder->getProductOrders();
        
        $arrProductId = array();
        foreach ($arrProductOrder as $objProductOrder) {
            $arrProductId[] = $objProductOrder->getProduct()->getId();
        }
        
        $arrProduct = $objEM->getRepository('AppBundle:Product')->findAllById($arrProductId);
        $arrBinNames = array();
        foreach ($arrProduct as $objProduct) {
            $arrBinNames[] = $objProduct->getBin()->getName();
        }
        
        $arrRack = $objEM->getRepository('AppBundle:Rack')->findAll();
        $objOptimumRoute;
        $targetPackingStation;
        $intRouteLenght = 0;
        
        foreach ($arrRack as $objRack) {
            $packingStation = json_decode($objRack->getPackingStation(), true);
            $objPosition = new Position($packingStation['x'], $packingStation['y'], 0);
            $objRoute = $objWarehouseService->getOptimumRoute($objOriginalMap, $objPosition, $arrBinNames, count($arrBinNames));
            
            if(0 == $intRouteLenght || $objRoute->getDistance() < $intRouteLenght) {
                $intRouteLenght = $objRoute->getDistance();
                $objOptimumRoute = $objRoute;
                $targetPackingStation = $packingStation;
            }
        }
        $arrProduct = $objWarehouseService->sortProduct($objOptimumRoute, $arrProduct);
        $objProduct = new \AppBundle\Entity\Product();
        $objProduct->setName($targetPackingStation['name']);
        $arrProduct[] = $objProduct;
        
        $arrWarehouseCollection = array();        
        foreach ($objOptimumRoute->getArrPath() as $objPath) {
            $arrWarehouseCollection[] = $objWarehouseService->updateMap($objOriginalMap, $objPath, $arrRack);
        }
        
        return array(
            'strPackingStationName' => $targetPackingStation['fullName'],
            'arrWarehouseCollection' => $arrWarehouseCollection,
            'arrProduct' => $arrProduct,
            'intTotalDistance' => $objOptimumRoute->getDistance(),
        );
    }
    
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
