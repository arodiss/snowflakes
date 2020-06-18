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
		$frozen = imagecolorallocate($gd, 185, 184, 181);
		$free = imagecolorallocate($gd, 0, 255, 255); 
		foreach ($molecules as $molecule) {
			if ($molecule->getState() == Molecule::MOLECULE_STATE_FREE) {
				imagesetpixel($gd, $molecule->getX(), $molecule->getY(), $free);
			} else {
				imagesetpixel($gd, $molecule->getX(), $molecule->getY(), $frozen);
			}
		}
		imagepng($gd, $outputFilename);
	}
}
