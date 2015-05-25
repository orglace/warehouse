<?php
/**
 * Created by PhpStorm.
 * User: Luis
 * Date: 5/10/2015
 * Time: 9:25 PM
 */

namespace AppBundle\Service;

use AppBundle\Model\Map;
use AppBundle\Model\Route as MapRoute;
use AppBundle\Model\Path;
use AppBundle\Model\Cell;


class WarehouseMap {

    private $objDoctrineService;
    private $intBestDistance;
    private $objBestRoute;
    
    public function __construct($objDoctrineService) {
        $this->objDoctrineService = $objDoctrineService;
        $intBestDistance = -1;
        $objBestRoute = null;
    }

    
    /**
     * Action build and draw a Warehouse Map from Data Base Information
     * @param type $objEM
     * @return Map
     */
    
    public function createMap() 
    {
        $objEM = $this->objDoctrineService->getManager();
                
        $arrProductBin = $objEM->getRepository('AppBundle:ProductBin')->findAll();
        $arrRack = $objEM->getRepository('AppBundle:Rack')->findAll();
        
        $intBinNamesCount = count($arrRack)*2;
        $intBinAmount = count($arrRack[0]->getProductBins())/2;
        $intWidth = $intBinNamesCount + floor($intBinNamesCount/2) + $intBinNamesCount%2 + 1;
        $intHeight = $intBinAmount + 2;
        
        $objOriginalMap = new Map($intWidth, $intHeight, $arrProductBin); 
        return $objOriginalMap;
    }
    
    /**
     * Function that update a Warehouse Map with the a path and all the packing stations imformation 
     * 
     * @param Map $objMap
     * @param type $objPath
     * @param type $arrRack
     * @return type
     */
    
    public function updateMap(Map $objMap, $objPath, $arrRack) {
       
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
    
    /**
     * Function that return the optimum route from a packing station position to a array of bins
     * 
     * @param type $objMap
     * @param type $objPosition
     * @param type $arrBinNames
     * @return type
     */
    public function getOptimumRoute($objMap, $objPosition, $arrBinNames, $intLength) 
    {   
        $this->intBestDistance = -1;
        $this->objBestRoute = null;
        $this->findOptimumRoute($objMap, $objPosition, $arrBinNames, $intLength);

        return $this->objBestRoute;
    }
    
    /**
     * Function that find the optimum route from a packing station position to a array of bins
     * to optain the optimum route was used Heap's algorithm(generating all possible permutations of some given length)
     * 
     * @param type $objMap
     * @param type $objPosition
     * @param type $arrBinNames
     * @return type
     */
    private function findOptimumRoute($objMap, $objPosition, $arrBinNames, $intLength) 
    {   
        if (1 == $intLength) {
            $objCurrentRoute = $this->createRoute($objMap, $objPosition, $arrBinNames);
            $intCurrentDistance = $objCurrentRoute->getDistance();
            if (-1 == $this->intBestDistance || $this->intBestDistance > $intCurrentDistance) {
                $this->intBestDistance = $intCurrentDistance;
                $this->objBestRoute = $objCurrentRoute;
            }
        } else {
            for ($i = 0; $i < $intLength; $i++) {
                $this->findOptimumRoute($objMap, $objPosition, $arrBinNames, $intLength - 1);
                if (0 != $intLength%2) {
                    $intValue = $arrBinNames[$i];
                    $arrBinNames[$i] = $arrBinNames[$intLength - 1];
                    $arrBinNames[$intLength - 1] = $intValue;
                } else {
                    
                    $intValue = $arrBinNames[0];
                    $arrBinNames[0] = $arrBinNames[$intLength - 1];
                    $arrBinNames[$intLength - 1] = $intValue;
                }
            }
        }
    }
    
    /**
     * Function that create a route from a packing station position and array of bins
     * 
     * @param type $objMap
     * @param type $objPosition
     * @param type $arrBinNames
     * @return MapRoute
     */
    
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
    
    /**
     * Function that order a product array from a pick sheet route
     * 
     * @param type $objOptimumRoute
     * @param type $arrProduct
     * @return type
     */
    
    public function sortProduct($objOptimumRoute, $arrProduct) 
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
}