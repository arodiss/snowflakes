<?php

namespace Snowflakes;

class Snowflake
{
	SNOWFLAKE_STATE_FREE = 0;
	SNOWFLAKE_STATE_FROZEN = 1;

	/**
	 * @return int
	 */
	public function getX() {
		throw RuntimeException("Not implemented");
	}

	/**
         * @return int
         */
        public function getY() {
                throw RuntimeException("Not implemented");
        }

	/**
         * @return int
	 */
	public function getState() {
                throw RuntimeException("Not implemented");
        }
}
