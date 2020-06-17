<?php

namespace Snowflakes;

class Engine
{
    /**
    * Creates new engine instance
    * Coordinates go from -width to +width and from -height to +height, with (0, 0) always being the center of crystallization.
    *
    * @param int            $width
    * @param int            $height
    * @param int            $numMolecules If not provided the reasonable default is used
     *@param array|null    $crystalCenters
    */
    public function __construct($width, $height, $numMolecules = null, $crystalCenters = null) {
        throw new \RuntimeException("Not implemented");
    }

    /**
    * Run the entire system one step forward
    */
    public function step() {
        throw new \RuntimeException("Not implemented");
    }

    /**
    * @return Molecule[]
    */
    public function getMolecules() {
        throw new \RuntimeException("Not implemented");
    }
} 
