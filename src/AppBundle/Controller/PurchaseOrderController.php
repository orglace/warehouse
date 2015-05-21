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
use AppBundle\Entity\ProductOrder;
use AppBundle\Entity\PurchaseOrder;
use AppBundle\Model\Map;
use AppBundle\Model\Path;
use AppBundle\Model\Position;
use AppBundle\Model\Route as MapRoute;
use AppBundle\Model\Cell;

/**
 * Description of PurchaseOrderController
 *
 * @author Luis
 */

/**
 * PurchaseOrder controller.
 *
 * @Route("/order")
 */
class PurchaseOrderController extends Controller {
    //put your code here
    
    /**
     * Lists all ProductOrder entities.
     *
     * @Route("/warehouse/map", name="warehouse_map")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {
        $objEM = $this->getDoctrine()->getManager();
        $objOriginalMap = $this->createMap($objEM);
        $arrWarehouse = $objOriginalMap->getArrWarehouse();
        
        $arrRack = $objEM->getRepository('AppBundle:Rack')->findAll();
        
        $arrWarehouse = $this->updateMap($objOriginalMap, null, $arrRack);
        
        return array(
            'arrWarehouse' => $arrWarehouse,
        );
    }
    
    /**
     * Lists all ProductOrder entities.
     *
     * @Route("/warehouse/{id}/route", name="warehouse_map_route")
     * @Method("GET")
     * @Template()
     */
    public function routeAction($id)
    {
        $objEM = $this->getDoctrine()->getManager();
        $objOriginalMap = $this->createMap($objEM);
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
            $objRoute = $this->getOptimumRoute($objOriginalMap, $objPosition, $arrBinNames);
            
            if(0 == $intRouteLenght || $objRoute->getDistance() < $intRouteLenght) {
                $intRouteLenght = $objRoute->getDistance();
                $objOptimumRoute = $objRoute;
                $targetPackingStation = $packingStation;
            }
        }
        $arrProduct = $this->sortProduct($objOptimumRoute, $arrProduct);
        $objProduct = new \AppBundle\Entity\Product();
        $objProduct->setName($targetPackingStation['name']);
        $arrProduct[] = $objProduct;
        
        $arrWarehouseCollection = array();        
        foreach ($objOptimumRoute->getArrPath() as $objPath) {
            $arrWarehouseCollection[] = $this->updateMap($objOriginalMap, $objPath, $arrRack);
        }
        
        return array(
            'strPackingStationName' => $targetPackingStation['name'],
            'arrWarehouseCollection' => $arrWarehouseCollection,
            'arrProduct' => $arrProduct,
            'intTotalDistance' => $objOptimumRoute->getDistance(),
        );
    }
    
    private function createMap($objEM) 
    {
        $arrProductBin = $objEM->getRepository('AppBundle:ProductBin')->findAll();
        $arrRack = $objEM->getRepository('AppBundle:Rack')->findAll();
        
        $intBinNamesCount = count($arrRack)*2;
        $intBinAmount = count($arrRack[0]->getProductBins())/2;
        $intWidth = $intBinNamesCount + floor($intBinNamesCount/2) + $intBinNamesCount%2 + 1;
        $intHeight = $intBinAmount + 2;
        
        $objOriginalMap = new Map($intWidth, $intHeight, $arrProductBin); 
        return $objOriginalMap;
    }
    
    private function updateMap(Map $objMap, $objPath, $arrRack) {
       
        $intHeight = $objMap->getIntHeight();
        $intWidth = $objMap->getIntWidth();
        $arrNewMap = $objMap->getArrWarehouse();
        
        for ($i = 0; $i < $intHeight; $i++) {
            for ($j = 0; $j < $intWidth; $j++) {
               $strValue = $arrNewMap[$i][$j];
               if (" " == $strValue) {
                   $arrNewMap[$i][$j] = new Cell($strValue, " ");
               } else {
                   $arrNewMap[$i][$j] = new Cell($strValue, "bin");
               }
            } 
        }
        
        if (isset($objPath))
            foreach ($objPath->getArrPosition() as $objPosition) {
                $arrNewMap[$objPosition->getX()][$objPosition->getY()] = new Cell(" ", "path");
            }
        
        foreach ($arrRack as $objRack) {
            $packingStation = json_decode($objRack->getPackingStation(), true);
            $objCurrentCell = $arrNewMap[$packingStation['x']][$packingStation['y']];
            $arrNewMap[$packingStation['x']][$packingStation['y']] = new Cell($packingStation['name'], $objCurrentCell->strClass." ps");
            
        }
        return array_reverse($arrNewMap);
    }
    
    private function getOptimumRoute($objMap, $objPosition, $arrBinNames) 
    {   
        $lenght = count($arrBinNames);
        $intDistance;
        $objRoute = new MapRoute($objMap);
        
        for ($i = 0; $i < $lenght; $i++) {
            for ($j = 0; $j < $lenght-1; $j++) {
                $objCurrentRoute = $this->createRoute($objMap, $objPosition, $arrBinNames);
                $intCurrentDistance = $objCurrentRoute->getDistance();
                //dump($intCurrentDistance);
                if (!isset($intDistance) || $intDistance > $intCurrentDistance) {
                    $intDistance = $intCurrentDistance;
                    $objRoute = $objCurrentRoute;
                }
                $strBinNamePivot = $arrBinNames[$j];
                $arrBinNames[$j] = $arrBinNames[$j+1];
                $arrBinNames[$j+1] = $strBinNamePivot;
            }
        }
        
        return $objRoute;
    }
    
    private function createRoute($objMap, $objPosition, $arrBinNames) {
        
        $lenght = count($arrBinNames);
        $objRoute = new MapRoute($objMap);
        $objCurrentPath;
        $objCurrentPosition = $objPosition;
        
        for ($i = 0; $i < $lenght; $i++) {
            $objCurrentPath = new Path($objMap, $objCurrentPosition, $arrBinNames[$i]);
            $objRoute->addPath($objCurrentPath);
            $objCurrentPosition = $objCurrentPath->getLastPosition();
        }
        $arrValues = array_values($arrBinNames);
        $objCurrentPath = new Path($objMap, $objPosition, end($arrValues));
        $objCurrentPath->setReverseArrPosition();
        $objRoute->addPath($objCurrentPath);
        return $objRoute;
    }
    
    private function sortProduct($objOptimumRoute, $arrProduct) 
    {
        $arrPath = $objOptimumRoute->getArrPath();
        $intProductQuantity = count($arrProduct);
        
        unset($arrPath[$intProductQuantity]);
        
        for ($i = 0; $i < $intProductQuantity; $i++) {
            for ($j = $i; $j < $intProductQuantity; $j++) {
                if ($arrProduct[$j]->getBin()->getName() == $arrPath[$i]->getStrBinName()) {
                    $objProduct = $arrProduct[$i];
                    $arrProduct[$i] = $arrProduct[$j];
                    $arrProduct[$j] = $objProduct;
                    break;
                }
            }
        }
        
        return $arrProduct;
    }


    /**
     * Creates a new ProductOrder entity.
     *
     * @Route("/product/add", name="order_product_add")
     * 
     * @Template("AppBundle:PurchaseOrder:add.html.twig")
     */
    public function addProductAction(Request $request)
    {
        $session = $request->getSession();
    	$jsonPurchaseList = $session->get('purchaseList', null);
        
        $objEM = $this->getDoctrine()->getManager();
        $arrProduct = $objEM->getRepository('AppBundle:Product')->findAll();
      
        $arrPurchaseList = isset($jsonPurchaseList)? json_decode($jsonPurchaseList, true): array();
        $arrPurchaseProduct = $objEM->getRepository('AppBundle:Product')->findAllById(array_keys($arrPurchaseList));
        $arrProductChoise = $this->getProductChoise($arrProduct);
        
    	$defaultData = array('message' => 'Product Choose');
    	$form = $this->createFormBuilder($defaultData)
            ->add('product', 'choice', array('choices' => $arrProductChoise,
                'placeholder' => 'Choose a Product',
                'empty_data' => null,
            ))
            ->add('quantity', 'integer', array('data'  => 1, 'empty_data' => 1,))
            ->add('add', 'submit')
            ->getForm();

    	$form->handleRequest($request);
    	
        if ($form->isValid()) {
           $data = $form->getData();
           $intProductId = intval($data["product"]);
    	   $intQuantity = intval($data["quantity"]);
           $objProduct = $objEM->getRepository('AppBundle:Product')->find($intProductId);
           
           if ($intQuantity < $objProduct->getStockLevel()) {
               $arrPurchaseList[$intProductId] = $intQuantity;
           } else {
               $this->addFlash('notice','There is no enough product '.$objProduct->getName().'!');
           }
           
           $arrPurchaseProduct = $objEM->getRepository('AppBundle:Product')->findAllById(array_keys($arrPurchaseList));
           $session->set('purchaseList', json_encode($arrPurchaseList));
    	}
        
        $strView = $request->isMethod("GET")? 'AppBundle:PurchaseOrder:add.html.twig': 'AppBundle:PurchaseOrder:add_form.html.twig';
        
        return $this->render($strView, array(
            'arrProduct' => $arrPurchaseProduct,
            'arrQuantity' => array_values($arrPurchaseList),
            'addDisabled' => count($arrPurchaseProduct) >= 5? true: false,
            'form'   => $form->createView(),
        ));
    }
       
    private function getProductChoise($arrProduct) 
    {
        $arrProductChoise = array();
        foreach ($arrProduct as $objProduct) {
           $arrProductChoise[$objProduct->getId()] = $objProduct->getName(); 
        }
        return $arrProductChoise;
    }
    
    /**
     * Delete a product from order list.
     *
     * @Route("/product/{id}/delete", name="order_product_delete")
     * 
     */
    public function deleteProductAction(Request $request, $id) 
    {
        $session = $request->getSession();
    	$jsonPurchaseList = $session->get('purchaseList', null);
        
        $arrPurchaseList = isset($jsonPurchaseList)? json_decode($jsonPurchaseList, true): array();
        unset($arrPurchaseList[$id]);
        $session->set('purchaseList', json_encode($arrPurchaseList));
        
        return $this->redirect($this->generateUrl('order_product_add'));
    }
    
    /**
     * Process a product order list.
     *
     * @Route("/product/buy", name="order_product_buy")
     * @Template("AppBundle:ProductOrder:list.html.twig")
     */
    public function buyProductAction(Request $request) 
    {
        $session = $request->getSession();
    	$jsonPurchaseList = $session->get('purchaseList', null);
        
        $arrPurchaseList = isset($jsonPurchaseList)? json_decode($jsonPurchaseList, true): array();

        if(count($arrPurchaseList) == 5) {
            $objEM = $this->getDoctrine()->getManager();
            $arrPurchaseProduct = $objEM->getRepository('AppBundle:Product')->findAllById(array_keys($arrPurchaseList));
            $objUser = $objEM->getRepository('AppBundle:User')->findOneByUsername("admin");
            
            $objPurchaseOrder = new PurchaseOrder();
            $objPurchaseOrder->setUser($objUser);
            $objEM->persist($objPurchaseOrder);
            
            $arrPurchaseQuantity = array_values($arrPurchaseList);
            
            foreach ($arrPurchaseProduct as $objProduct) {
                $objProductOrder = new ProductOrder();
                $objProductOrder->setProduct($objProduct);
                $objProductOrder->setPurchaseOrder($objPurchaseOrder);
                $objProductOrder->setQuantity($arrPurchaseList[$objProduct->getId()]);
                $objEM->persist($objProductOrder);
                
                $objProduct->decreaseStockLevel(intval(array_shift($arrPurchaseQuantity)));
            }
            $objEM->flush();
            $session->set('purchaseList', null);
            return $this->redirect($this->generateUrl('order_product_list'));
        } else {
            $this->addFlash('notice','The order must have 5 product!');
        }
        return $this->redirect($this->generateUrl('order_product_add'));
    }
    
    /**
     * Process a product order list.
     *
     * @Route("/product/list", name="order_product_list")
     * 
     */
    public function listProductAction(Request $request) 
    {
        $objEM = $this->getDoctrine()->getManager();
        $arrPurchaseOrder = $objEM->getRepository('AppBundle:PurchaseOrder')->findAll();
        
        return $this->render('AppBundle:PurchaseOrder:list.html.twig', array(
            'arrPurchaseOrder' => $arrPurchaseOrder
        ));
    }
    
    /**
     * Get the last five sold products.
     *
     * @Route("/product/last5", name="order_last_five")
     * @Method("GET")
     * @Template()
     */
    public function lastFiveAction() {
       
        $repository = $this->getDoctrine()->getRepository('AppBundle:PurchaseOrder');

        $query = $repository->createQueryBuilder('p')
            ->where('p.user != :user')
            ->setParameter('user', '-1')
            ->orderBy('p.date', 'DESC')
            ->setFirstResult(0)
            ->setMaxResults(1)
            ->getQuery();

        $objPurchaseOrder = $query->getSingleResult();
        
        return array(
            'arrProductOrder' => $objPurchaseOrder->getProductOrders(),
        );
    }
}
