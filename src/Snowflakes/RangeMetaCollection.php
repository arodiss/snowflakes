<?php

namespace Snowflakes;

class RangeMetaCollection
{
	private $collections;

	public function __construct() {
		$this->collections = [];
	}

	public function contains($x, $y) {
		if (isset($this->collections[$x])) {
			return $this->collections[$x]->contains($y);                	
		}
		return false;
	}

	public function add($x, $y) {
		if (false == isset($this->collections[$x])) {
			$this->collections[$x] = new RangeCollection();               	
		}
		$this->collections[$x]->add($y);
	}

	public function flush() {
		ksort($this->collections);
		foreach ($this->collections as $i => $collection) {
			$collection->flush();
		}
	}
}
