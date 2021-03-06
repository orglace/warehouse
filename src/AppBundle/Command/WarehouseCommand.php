<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use AppBundle\Model\Map;
use AppBundle\Model\Route;
use AppBundle\Model\Path;
use AppBundle\Model\Position;
/**
 * Description of WarehouseCommand
 *
 * @author Luis
 */
class WarehouseCommand extends ContainerAwareCommand{
    //put your code here
    private $intBinAmount = 10;
    private $arrBinNames = array("A", "B", "C", "D", "E", "F");
    private $arrWarehouse;
    private $arrRoad;
    private $intWidth;
    private $intHeight;
    
    private $intBestDistance = -1;
    private $objBestRoute;


    protected function configure()
    {
        $this
            ->setName('warehouse:generate:database')
            ->setDescription('Generate all warehouse database')
            /*->addArgument(
                'name',
                InputArgument::OPTIONAL,
                'Who do you want to greet?'
            )
            ->addOption(
                'yell',
                null,
                InputOption::VALUE_NONE,
                'If set, the task will yell in uppercase letters'
            )*/
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /*$name = $input->getArgument('name');
        if ($name) {
            $text = 'Hello '.$name;
        } else {
            $text = 'Hello';
        }

        if ($input->getOption('yell')) {
            $text = strtoupper($text);
        }*/
        
        /*$this->initWarehouse();
        $distance = $this->distance(new Position(0, 4, 0), "D10");
        $this->printWarehouse($output);
        
        $output->writeln("The better distance is: ".$distance);*/
        
        
        
        
        
        $objEM = $this->getContainer()->get('doctrine')->getManager();
        $arrProductBin = $objEM->getRepository('AppBundle:ProductBin')->findAll();
        $arrRack = $objEM->getRepository('AppBundle:Rack')->findAll();
        
        $intBinNamesCount = count($arrRack)*2;
        $intBinAmount = count($arrRack[0]->getProductBins())/2;
        $intWidth = $intBinNamesCount + floor($intBinNamesCount/2) + $intBinNamesCount%2 + 1;
        $intHeight = $intBinAmount + 2;
        
        $objOriginalMap = new Map($intWidth, $intHeight, $arrProductBin);
        $arrBinNames = array("B3", "B1", "E1", "E3", "F1");
        $this->getOptimumRoute($objOriginalMap, new Position(0, 7, 0), $arrBinNames, 5);
        
        //$this->printMap($this->updateMap($objOriginalMap, $objPath), $output);
        
        //$output->writeln("The better distance is: ".$intDistance);
        //$this->printPath($objPath, $output);
        
        //$this->printMap($objOriginalMap->getArrWarehouse(), $output);
        //$output->writeln("The better distance is: ".$objOptimumRoute->getDistance());
        foreach ($this->objBestRoute->getArrPath() as $objPath) {
            $this->printMap($this->updateMap($objOriginalMap, $objPath), $output);
            $this->printPath($objOriginalMap, $objPath, $output);
        }
        
        $output->writeln("The better route has a distance of: ".$this->objBestRoute->getDistance());
        
        
        
        
        
        /*$objPurchase = array(14 => 10, 25 => 2);
        $jsonPurchase = json_encode($objPurchase);
        
        dump($jsonPurchase);
        
        $objPurchaseFromJson = json_decode($jsonPurchase, true);
        $objPurchaseFromJson[20] = 30;
        dump($objPurchaseFromJson);
        
        foreach ($objPurchaseFromJson as $key => $value) {
            $output->writeln($key." => ".$value);
        }
        
        $value = null;
        $output->writeln(isset($value));
        //$output->writeln($objPositionFromJson->{'x'}[1]);
        //$output->writeln("The x value is: ".$objPositionFromJson.getX());
        $intValue = intval("25");
        dump($intValue);*/
        //$this->permutation(4, array("A", "B", "C", "D"), $output);
    }
    
    private function updateMap(Map $objMap, Path $objPath) {
       
        $arrNewMap = $objMap->getArrWarehouse();
        foreach ($objPath->getArrPosition() as $objPosition) {
            $arrNewMap[$objPosition->getX()][$objPosition->getY()] = "*";
        }
        return array_reverse($arrNewMap);
    }
    
    private function printMap($arrMap, $output) {
        $table = $this->getHelper('table');
        $table->setRows($arrMap);
        $table->render($output);
    }
    
    private function printPath(Map $objMap, Path $objPath, $output) {
        foreach ($objPath->getArrPosition() as $objPosition) {
            $output->write('['.$objPosition->getX().', '.$objPosition->getY().']'.'=>');
        }
        $output->writeln('');
        $output->writeln('Path distance: '.  strval(count($objPath->getArrPosition())-1));
        $output->writeln('Product Bin: '.$objPath->getStrBinName());
    }
    
    /**
     * Function get the optimum route using Heap's algorithm(generating all possible permutations of some given length)
     * @param type $objMap
     * @param type $objPosition
     * @param type $arrBinNames
     * @return type
     */
    private function getOptimumRoute($objMap, $objPosition, $arrBinNames, $intLength) 
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
                $this->getOptimumRoute($objMap, $objPosition, $arrBinNames, $intLength - 1);
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
    
    private function createRoute($objMap, $objPosition, $arrBinNames) {
        
        $lenght = count($arrBinNames);
        $objRoute = new Route($objMap);
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
    
    private function permutation ($intN, $arrBin, $output) {
        
        if (1 == $intN) {
            $output->write($arrBin);
            $output->writeln(" ");
        } else {
            for ($i = 0; $i < $intN; $i++) {
                $this->permutation($intN - 1, $arrBin, $output);
                if (0 != $intN%2) {
                    $intValue = $arrBin[$i];
                    $arrBin[$i] = $arrBin[$intN - 1];
                    $arrBin[$intN - 1] = $intValue;
                } else {
                    
                    $intValue = $arrBin[0];
                    $arrBin[0] = $arrBin[$intN - 1];
                    $arrBin[$intN - 1] = $intValue;
                }
            }
        }
    }
    /*protected function initWarehouse() 
    {
        $intBinNamesCount = count($this->arrBinNames);
        $this->intWidth = $intBinNamesCount + floor($intBinNamesCount/2) + $intBinNamesCount%2 + 1;
        $this->intHeight = $this->intBinAmount + 2;        
        
        for ($x = 0; $x < $this->intHeight; $x++) {
            for ($y = 0; $y < $this->intWidth; $y++) 
                $this->arrWarehouse[$x][$y] = '#';
        }
        
        for ($x = 1; $x < $this->intHeight - 1; $x++) {
            for ($i = 0, $y = 1; $i < $intBinNamesCount; $i+=2,$y+=3) {
                
                $this->arrWarehouse[$x][$y] = $this->arrBinNames[$i].$x;
                if (isset($this->arrBinNames[$i+1]))
                    $this->arrWarehouse[$x][$y+1] = $this->arrBinNames[$i+1].$x;
            }
        }
    }
    
    protected function distance($objPosition, $strBinName) 
    {
        $arrXScroll = array(0, 0, 1, -1);
        $arrYScroll = array(1, -1, 0, 0);
        $arrVisited;
        $quePositions[0] = $objPosition;
        $this->arrRoad[$objPosition->getX()][$objPosition->getY()] = new Position(-1, -1, -1);
        
        while (count($quePositions) != 0) {
            $objCurrent = array_shift($quePositions);
            $x = $objCurrent->getX();
            $y = $objCurrent->getY();
            if((isset($this->arrWarehouse[$x][$y-1]) && $this->arrWarehouse[$x][$y-1] == $strBinName) || (isset($this->arrWarehouse[$x][$y+1]) && $this->arrWarehouse[$x][$y+1] == $strBinName)) {
                $this->printPath($objCurrent);
                return $objCurrent->getDistance();
            }
            $arrVisited[$x][$y] = true;
            
            for ($i = 0; $i < 4; $i++) {
                $nx = $arrXScroll[$i] + $x;
                $ny = $arrYScroll[$i] + $y;
                
                if($nx >= 0 && $nx < $this->intHeight && $ny >= 0 && $ny < $this->intWidth && $this->arrWarehouse[$nx][$ny] == "#" && !isset($arrVisited[$nx][$ny])) {
                    array_push($quePositions, new Position($nx, $ny, $objCurrent->getDistance() + 1));
                    $this->arrRoad[$nx][$ny] = $objCurrent;                   
                }
            }
        }
        return -1;
    }
    
    protected function printPath2($position) {
        if(-1 != $position->getDistance()) {
            $this->arrWarehouse[$position->getX()][$position->getY()] = "*";
            $this->printPath($this->arrRoad[$position->getX()][$position->getY()]);
        }
    }

    protected function printWarehouse($output) 
    {
        $table = $this->getHelper('table');
        $table->setRows($this->arrWarehouse);
        $table->render($output);
 
    }*/
}
