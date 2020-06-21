<?php

namespace Snowflakes;

class Engine
{
    const DEFAULT_MOLECULES_DENSITY_COEFFICIENT = 0.1;

    private $width;
    private $height;
    private $freezingRanges;
    private $freeMolecules = [];
    private $frozenMolecules = [];

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
            throw new \InvalidArgumentException("Width should be positive integer");
        }
        $this->width = $width;

        if (! is_int($height) || $height <= 0) {
            throw new \InvalidArgumentException("Height should be positive integer");
        }
        $this->height = $height;

        $this->freezingRanges = new RangeMetaCollection();
        if ($crystalCenters === null) {
            $crystalCenters = [[(int) ceil($width / 2), (int) ceil($height / 2)]];
        }
        foreach ($crystalCenters as $crystalCenter) {
            if (is_array($crystalCenter) && count($crystalCenter) == 2 &&
                is_int($crystalCenter[0]) && $crystalCenter[0] < $width &&
                is_int($crystalCenter[1]) && $crystalCenter[0] < $height) {
                    $center = new Molecule($crystalCenter[0], $crystalCenter[1]);
                    $center->markAsCrystalCenter();
                    $this->frozenMolecules[] = $center;
                    $this->addNeighborsToFreezingSpots($center);
           } else {
               //for now several valid but identical crystal centers are ok
               throw new \InvalidArgumentException("Wrong format for crystal centers");
           }
        }

        $square = $width * $height;
        if ($numMolecules === null) {
            $numMolecules = intval($square * $this::DEFAULT_MOLECULES_DENSITY_COEFFICIENT);
        }
        if ($numMolecules + count($crystalCenters) > $square) {
            throw new \InvalidArgumentException("Too many molecules and crystal centers for this area.");
        }

        $generatedMolecules = [];
        foreach (range(0, $width) as $x) {
            foreach (range(0, $height) as $y) {
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
//        there could potentially be more than one molecule in each point
        foreach ($this->freeMolecules as $molecule) {
            $possibleCoordinates = $molecule->getNeighboringCoordinates();
            $newCoordinates = $possibleCoordinates[array_rand($possibleCoordinates)];
            $molecule->setX(max(min($newCoordinates[0], $this->width), 0));
            $molecule->setY(max(min($newCoordinates[1], $this->height), 0));
        }

        $this->makeFreezing();
    }

    /**
    * @return Molecule[]
    */
    public function getMolecules() {
        return $this->frozenMolecules + $this->freeMolecules;
    }

    /**
     * @return float
     */
    public function getFreeMoleculesShare()
    {
        return count($this->freeMolecules) / ( count($this->freeMolecules) + count($this->frozenMolecules));
    }

    /**
     * @param Molecule $molecule
     */
    private function addNeighborsToFreezingSpots(Molecule $molecule) {
        foreach (range(max($molecule->getX()-1, 0), min($molecule->getX()+1, $this->width)) as $x) {
            foreach (range(max($molecule->getY()-1, 0), min($molecule->getY()+1, $this->height)) as $y) {
                $this->freezingRanges->add($x, $y);
            }
        }
    }

    private function makeFreezing() {
        foreach ($this->freeMolecules as $molecule) {
            if ($this->shouldFreeze($molecule)) {
                $molecule->freeze();
                $this->frozenMolecules[] = $molecule;
                unset($this->freeMolecules[array_search($molecule, $this->freeMolecules)]);
                $this->addNeighborsToFreezingSpots($molecule);
            }
        }
	$this->freezingRanges->flush();
    }

    /**
     * @param $molecule
     * @return bool
     */
    private function shouldFreeze(Molecule $molecule) {
        return $this->freezingRanges->contains($molecule->getX(), $molecule->getY());
    }
} 
