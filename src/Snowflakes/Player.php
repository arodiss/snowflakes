<?php

namespace Snowflakes;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\ConsoleOutput;

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
	public function __construct($width, $height, $numMolecules, $maxFrames, $stopWhenCrystalized, $displayNthStep, $fps = self::DEFAULT_FPS, $out = self::DEFAULT_VIDEO_FILE_NAME) {
		$this->engine = new Engine($width, $height, $numMolecules);
		$this->maxFrames = $maxFrames;
		$this->displayNthStep = $displayNthStep;
		$this->frameNameLength = (int) ceil(log10($this->maxFrames));
		$this->renderer = new Renderer($width, $height);
		$this->stopWhenCrystalized = $stopWhenCrystalized;
		$this->fps = $fps ?: self::DEFAULT_FPS;
		$this->out = $out ?: self::DEFAULT_VIDEO_FILE_NAME;
	}

	public function play() {
		$frames = [];
		$repeatLastFrame = $this->fps * 5;
		$progressBar = new ProgressBar(new ConsoleOutput(), $this->maxFrames);
		for ($frame = 1; $frame < $this->maxFrames; $frame++) {
			$frames[] = sprintf(
				self::FRAME_FILE_NAME_PATTERN,
				str_pad($frame, $this->frameNameLength, "0", STR_PAD_LEFT)
			);
			$molecules = $this->engine->getMolecules();
			$this->renderer->render($molecules, end($frames));
			if ($this->engine->getFreeMoleculesShare() < 1 - $this->stopWhenCrystalized) {
				if ($repeatLastFrame > 0) {
					$repeatLastFrame = $repeatLastFrame - 1;
				} else {
					break;
				}
			} else {
				for ($i = 1; $i < $this->displayNthStep; $i++) {
					$this->engine->step();
				}
				$progressBar->advance();
			}
		}
		$progressBar->finish();
		$progressBar->clear();
		$this->concat();
		foreach ($frames as $frame) {
			unlink($frame);
		}
		$this->playVideo();
	}

	protected function concat() {
		echo "Concatenating frames..." . PHP_EOL;
		$command = sprintf(
			"ffmpeg -r %s -i %s -r 1 %s 2>&1",
			$this->fps,
			sprintf(self::FRAME_FILE_NAME_PATTERN, "%" . $this->frameNameLength . "d"),
			$this->out
		);
		echo $command . PHP_EOL;
		exec($command, $_);
	}
	
	protected function playVideo() {
		echo "Playing video..." . PHP_EOL;
		exec(sprintf(
			"ffplay %s -autoexit 2>&1",
			$this->out
		), $_);
		if ($this->out == self::DEFAULT_VIDEO_FILE_NAME) {
			unlink(self::DEFAULT_VIDEO_FILE_NAME);
		}
	}
}
