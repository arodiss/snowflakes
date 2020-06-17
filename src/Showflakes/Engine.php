<?php

namespace Snowflakes;

class Engine
{
	/**
	 * Creates new engine instance
	 * Coordinates go from -width to +width and from -height to +height, with (0, 0) always being the center of crystallization.
	 *
	 * @param int $width
	 * @param int $height
	 * @param int $numSnowflakes If not provided the reasonable default is used
	 */
	public function __construct($width, $height, $numSnowflakes = null) {
                throw RuntimeException("Not implemented");
	}

	/**
	 * Run the entire system one step forward
         */
        public function step() {
                throw RuntimeException("Not implemented");
        }

        /**
         * @return Snowflake[]
         */
        public function getSnowflakes() {
                throw RuntimeException("Not implemented");
        }
} 
