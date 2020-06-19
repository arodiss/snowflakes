#!/usr/bin/env php
<?php

use Snowflakes\Player;
use Symfony\Component\Console\Input\ArgvInput;

set_time_limit(0);
srand(42);

require __DIR__.'/../vendor/autoload.php';

$input = new ArgvInput();
$player = new Player(
	(int) $input->getParameterOption(['--width', '-w'], 200),
	(int) $input->getParameterOption(['--height', '-h'], 200),
	$input->getParameterOption(['--num-molecules', '-m'], null),
	(int) $input->getParameterOption(['--max-frames', '-s'], 10000),
	(float) $input->getParameterOption(['--stop-when-crystalized'], .5),
	(int) $input->getParameterOption(['--display-nth-step'], 5),
	$input->getParameterOption(['--fps'], null),
	$input->getParameterOption(['--out', '-o'], null)
);
$player->play();

