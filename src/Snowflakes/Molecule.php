<?php

namespace Snowflakes;

class Molecule
{
    const MOLECULE_STATE_FREE = 0;
    const MOLECULE_STATE_FROZEN = 1;
    const MOLECULE_STATE_CRYSTAL_CENTER = 2;

    private $x;
    private $y;
    private $state = 0;

    public function __construct($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
    * @return int
    */
    public function getX() {
        return $this->x;
    }

    /**
    * @return int
    */
    public function getY() {
        return $this->y;
    }

    /**
    * @return int
    */
    public function getState() {
        return $this->state;
    }

    /**
     * @param $x int
     */
    public function setX($x)
    {
        $this->x = $x;
    }

    /**
     * @param $y int
     */
    public function setY($y)
    {
        $this->y = $y;
    }

    public function freeze()
    {
        $this->state = $this::MOLECULE_STATE_FROZEN;
    }

    public function markAsCrystalCenter()
    {
        $this->state = $this::MOLECULE_STATE_CRYSTAL_CENTER;
    }

    public function getNeighboringCoordinates()
    {
        return [
            [$this->getX(), $this->getY() + 1],
            [$this->getX(), $this->getY() - 1],
            [$this->getX() + 1, $this->getY()],
            [$this->getX() - 1, $this->getY()],

            [$this->getX() + 1, $this->getY() + 1],
            [$this->getX() - 1, $this->getY() + 1],
            [$this->getX() + 1, $this->getY() - 1],
            [$this->getX() - 1, $this->getY() - 1],
        ];
    }
}
