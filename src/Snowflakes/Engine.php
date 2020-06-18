<?php

namespace Snowflakes;

class Engine
{
    const DEFAULT_MOLECULES_DENSITY_COEFFICIENT = 0.02;

    private $width;
    private $height;
    private $numMolecules;
    private $freeMolecules = [];
    private $frozenMolecules = [];
    private $freezingSpots = [];
    private $stepsCounter = 0;

    /**
    * Creates new engine instance
    * Coordinates go from 0 to +width and from 0 to +height.
    *
    * @param int            $width
    * @param int            $height
    * @param int            $numMolecules If not provided the reasonable default is used
     *@param array|null    $crystalCenters
    */
    public function __construct($width, $height, $numMolecules = null, $crystalCenters = null) {
        if ( ! is_int($width) || $width <= 0) {
            throw new \RuntimeException("Width should be positive integer");
        }
        $this->width = $width;

        if (! is_int($height) || $height <= 0) {
            throw new \RuntimeException("Height should be positive integer");
        }
        $this->height = $height;

        if ($crystalCenters === null) {
            $center = new Molecule(intval(ceil($width / 2)), intval(ceil($height / 2)));
            $center->markAsCrystalCenter();
            $this->frozenMolecules[] = $center;
            $this->addNeighborsToFreezingSpots($center);
            $crystalCenters = [$center];
        } else {
            foreach ($crystalCenters as $crystalCenter) {
                if (is_array($crystalCenter) && count($crystalCenter) == 2 &&
                    is_int($crystalCenter[0]) && $crystalCenter[0] < $width &&
                    is_int($crystalCenter[1]) && $crystalCenter[0] < $height) {
                        $center = new Molecule($crystalCenter[0], $crystalCenter[1]);
                        $center->markAsCrystalCenter();
                        $this->frozenMolecules[] = $center;
                        $this->addNeighborsToFreezingSpots($center);
                } else {
//                    for now several valid but identical crystal centers are ok
                    throw new \RuntimeException("Wrong format for crystal centers");
                }
            }
        }

        $square = $width * $height;
        if ($numMolecules === null) {
            $numMolecules = intval($square * $this::DEFAULT_MOLECULES_DENSITY_COEFFICIENT);
        }
        if ($numMolecules + count($crystalCenters) > $square) {
            throw new \RuntimeException("Too many molecules and crystal centers for this area.");
        } else {
            $this->numMolecules = $numMolecules;
            foreach ($this->frozenMolecules as $frozenMolecule) {
                $this->addNeighborsToFreezingSpots($frozenMolecule);
            }
        }

        $moleculesXs = range(0, $width);
        $moleculesYs = range(0, $height);

        $generatedMolecules = [];
        foreach ($moleculesXs as $x) {
            foreach ($moleculesYs as $y) {
                $generatedMolecules[] = new Molecule($x, $y);
            }
        }

        shuffle($generatedMolecules);
        $this->freeMolecules = array_slice($generatedMolecules, 0, $numMolecules);

        $this-> makeFreezing();
    }

    /**
    * Run the entire system one step forward
    */
    public function step() {
        $this->stepsCounter += 1;
//        step is made by 1 point, in four directions only;
//        there could potentially be more than one molecule in each point
        foreach ($this->freeMolecules as $molecule) {
            $possibleCoordinates = $molecule->getNeighboringCoordinates();
            $newCoordinates = $possibleCoordinates[array_rand($possibleCoordinates)];
            $molecule->setX($newCoordinates[0]);
            $molecule->setY($newCoordinates[1]);
        }

        $this-> makeFreezing();
    }

    /**
    * @return Molecule[]
    */
    public function getMolecules() {
        return $this->frozenMolecules + $this->freeMolecules;
    }

    /**
     * @param Molecule $molecule
     */
    private function addNeighborsToFreezingSpots(Molecule $molecule) {
        $this->freezingSpots = array_unique($this->freezingSpots + [$molecule->getNeighboringCoordinates()]);
    }

    /**
     * @param $min
     * @param $max
     * @param $quantity
     * @return array
     */
    private function UniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }

    private function makeFreezing() {
        foreach ($this->freeMolecules as $molecule) {
            if (in_array([$molecule->getX(), $molecule->getY()], $this->freezingSpots)) {
                $molecule->freeze();
                $this->frozenMolecules[] = $molecule;
                unset($this->freeMolecules[array_search($molecule, $this->freeMolecules)]);
                $this->addNeighborsToFreezingSpots($molecule);
            }
        }
    }

    /**
     * @return bool
     */
    public function areFurtherChangesPossible()
    {
        return count($this->freeMolecules) > 0;
    }

    public function debug()
    {
        echo 'steps: ' . $this->stepsCounter . PHP_EOL;
        echo 'free'. PHP_EOL;
        var_dump($this->freeMolecules);
        echo 'frozen'. PHP_EOL;
        var_dump($this->frozenMolecules);
        echo 'spots'. PHP_EOL;
        var_dump($this->freezingSpots);



//        echo 'steps: ' . $this->stepsCounter . PHP_EOL;
//        echo 'free' . count($this->freeMolecules) . PHP_EOL;
//        echo 'frozen' . count($this->frozenMolecules) . PHP_EOL;
//        echo 'spots' . count($this->freezingSpots) . PHP_EOL;
    }
} 
