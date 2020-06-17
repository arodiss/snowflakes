<?php

namespace Snowflakes;

class Molecule
{
    const SNOWFLAKE_STATE_FREE = 0;
    const SNOWFLAKE_STATE_FROZEN = 1;

    /**
    * @return int
    */
    public function getX() {
        throw  new \RuntimeException("Not implemented");
    }

    /**
    * @return int
    */
    public function getY() {
        throw new \RuntimeException("Not implemented");
    }

    /**
    * @return int
    */
    public function getState() {
        throw new \RuntimeException("Not implemented");
    }
}
