<?php

namespace Snowflakes;

class Player
{
	const FRAME_FILE_NAME_PATTERN = "frame-%s.png";
	const DEFAULT_VIDEO_FILE_NAME = "video.mp4";
	const DEFAULT_FPS = 8.;

	/**
	 * @var int $width
	 * @var int $height
	 * @var int $numMolecules
	 * @var int $maxSteps
	 * @var float $fps
	 * @var str $out
	*/
	public function __construct($width, $height, $numMolecules, $maxSteps, $fps = self::DEFAULT_FPS, $out = self::DEFAULT_VIDEO_FILE_NAME) {
		$this->engine = new Engine($width, $height, $numMolecules);
		$this->maxSteps = $maxSteps;
		$this->frameNameLength = ceil(log10($this->maxSteps));
		$this->renderer = new Renderer($width, $height);
		$this->fps = $fps ?: self::DEFAULT_FPS;
		$this->out = $out ?: self::DEFAULT_VIDEO_FILE_NAME;
	}

	public function play() {
		$frames = [];
		$repeatLastFrame = $this->fps * 5;
		for ($i = 1; $i < $this->maxSteps; $i++) {
			$frames[] = sprintf(
				self::FRAME_FILE_NAME_PATTERN,
				str_pad($i, $this->frameNameLength, "0", STR_PAD_LEFT)
			);
			$molecules = $this->engine->getMolecules();
			$this->renderer->render($molecules, end($frames));
			if ($this->noFree($molecules)) {
				if ($repeatLastFrame > 0) {
					$repeatLastFrame = $repeatLastFrame - 1;
				} else {
					break;
				}
			}
			$this->engine->step();
		}
		$this->concat();
		foreach ($frames as $frame) {
			unlink($frame);
		}
		$this->doPlay();
	}

	/**
	 * @param $molecules
	 * @return bool
	 */
	protected function noFree($molecules) {
		foreach ($molecules as $molecule) {
			if ($molecule->getState() == Molecule::MOLECULE_STATE_FREE) {
				return false;
			}
		}
		return true;
	}

	protected function concat() {
		echo "Concatenating frames..." . PHP_EOL;
		$command = sprintf(
			"ffmpeg -r %s -i %s -pix_fmt yuv420p -r 1 %s",
			$this->fps,
			sprintf(self::FRAME_FILE_NAME_PATTERN, "%" . $this->frameNameLength . "d"),
			$this->out
		);
		echo $command . PHP_EOL;
		exec($command);
	}
	
	protected function doPlay() {
		echo "Playing video..." . PHP_EOL;
		exec(sprintf(
			"ffplay %s -autoexit",
			$this->out
		));
		if ($this->out == self::DEFAULT_VIDEO_FILE_NAME) {
			unlink(self::DEFAULT_VIDEO_FILE_NAME);
		}
	}
}
