#!/usr/bin/env php
<?php

use Snowflakes\Player;
use Symfony\Component\Console\Input\ArgvInput;

set_time_limit(0);

require __DIR__.'/../vendor/autoload.php';

$input = new ArgvInput();
$player = new Player(
	$input->getParameterOption(['--width', '-w'], 200),
	$input->getParameterOption(['--height', '-h'], 200),
	$input->getParameterOption(['--num-molecules', '-m'], null),
	$input->getParameterOption(['--max-steps', '-s'], 10000),
	$input->getParameterOption(['--fps'], null),
	$input->getParameterOption(['--out', '-o'], null)
);
$player->play();

