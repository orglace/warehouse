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
     * Action that add a new product to and Purchase Order.
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
           
           if ($intQuantity <= $objProduct->getStockLevel()) {
               $arrPurchaseList[$intProductId] = $intQuantity;
           } else {
               $this->addFlash('notice','There is no enough product '.$objProduct->getName().'!');
           }
           
           $arrPurchaseProduct = $objEM->getRepository('AppBundle:Product')->findAllById(array_keys($arrPurchaseList));
           $session->set('purchaseList', json_encode($arrPurchaseList));
    	}
        
        ksort($arrPurchaseList);
        $strView = ($request->isMethod("GET") && !$request->isXmlHttpRequest())? 'AppBundle:PurchaseOrder:add.html.twig': 'AppBundle:PurchaseOrder:add_form.html.twig';
        
        return $this->render($strView, array(
            'arrProduct' => $arrPurchaseProduct,
            'arrQuantity' => array_values($arrPurchaseList),
            'addDisabled' => count($arrPurchaseProduct) >= 5? true: false,
            'form'   => $form->createView(),
        ));
    }
    
    /**
     * Function that create a choise array to a select widget from a product array;
     * 
     * @param type $arrProduct
     * @return type
     */
       
    private function getProductChoise($arrProduct) 
    {
        $arrProductChoise = array();
        foreach ($arrProduct as $objProduct) {
           $arrProductChoise[$objProduct->getId()] = $objProduct->getName(); 
        }
        return $arrProductChoise;
    }
    
    /**
     * Action that delete a product from a Purchase Order.
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
        
        $this->addFlash('redirect', true);
        
        
        return $this->redirect($this->generateUrl('order_product_add'));
    }
    
    /**
     * Action that save a Purchase Order.
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
            
            ksort($arrPurchaseList);
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
            return $this->redirect($this->generateUrl('order_list'));
        } else {
            $this->addFlash('notice','The order must have 5 product!');
        }
        return $this->redirect($this->generateUrl('order_product_add'));
    }
    
    /**
     * Action that list a Purchase Order.
     *
     * @Route("/list", name="order_list")
     * 
     */
    public function listAction(Request $request) 
    {
        $objEM = $this->getDoctrine()->getManager();
        $arrPurchaseOrder = $objEM->getRepository('AppBundle:PurchaseOrder')->findAll();
        
        $strView = !$request->isXmlHttpRequest()? 'AppBundle:PurchaseOrder:list.html.twig': 'AppBundle:PurchaseOrder:list_template.html.twig';
        return $this->render($strView, array(
            'arrPurchaseOrder' => $arrPurchaseOrder
        ));
    }
    
    /**
     * Action that delete a Purchase Order.
     *
     * @Route("/{id}/delete", name="order_delete")
     * @Method("GET")
     * @Template()
     */
    public function deleteAction($id) {
       
        $objEM = $this->getDoctrine()->getManager();
        $objPurchaseOrder = $objEM->getRepository('AppBundle:PurchaseOrder')->find($id);
        
        foreach ($objPurchaseOrder->getProductOrders() as $objProductOrder) {
            $intQuantity = $objProductOrder->getQuantity();
            $objProduct = $objProductOrder->getProduct();
            $objProduct->setStockLevel($objProduct->getStockLevel() + $intQuantity);
            
            $objEM->remove($objProductOrder);
        }
        $objEM->remove($objPurchaseOrder);
        $objEM->flush();
                
        return $this->redirect($this->generateUrl('order_list'));
    }
    
    /**
     * Action that get the last five sold products.
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
        
        $arrProductOrder;
        try {
            $objPurchaseOrder = $query->getSingleResult();
            $arrProductOrder = $objPurchaseOrder->getProductOrders();
        } catch (\Doctrine\ORM\NoResultException $exc) {
            $arrProductOrder = array();
        }
        
        return array(
            'arrProductOrder' => $arrProductOrder,
        );
    }     
}
