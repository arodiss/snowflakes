<?php

namespace Snowflakes;

class Renderer
{
	/**
	 * @var int $width
	 * @var int $height
	 */
	public function __construct($width, $height) {
		$this->width = $width;
		$this->height = $height;
	}

	/**
	 * @var Molecule[] $molecules
	 * @var str $outputFliename
	 */
	public function render($molecules, $outputFilename) {
		$gd = imagecreatetruecolor($this->width, $this->height);
		$free = imagecolorallocate($gd, 185, 184, 181);
		$frozen = imagecolorallocate($gd, 0, 255, 255);
		$center = imagecolorallocate($gd, 255, 64, 0);
		foreach ($molecules as $molecule) {
			if ($molecule->getState() == Molecule::MOLECULE_STATE_FREE) {
				imagesetpixel($gd, $molecule->getX(), $molecule->getY(), $free);
			} else if ($molecule->getState() == Molecule::MOLECULE_STATE_FROZEN) {
				imagesetpixel($gd, $molecule->getX(), $molecule->getY(), $frozen);
			} else if ($molecule->getState() == Molecule::MOLECULE_STATE_CRYSTAL_CENTER) {
				imagesetpixel($gd, $molecule->getX(), $molecule->getY(), $center);
			}
		}
		imagepng($gd, $outputFilename);
	}
}
