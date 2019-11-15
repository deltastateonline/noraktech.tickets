#!/usr/bin/env php
<?php
// application.php

require __DIR__.'/vendor/autoload.php';
require_once __DIR__ . '/configs/config.php';

use Symfony\Component\Console\Application;
use Console\ProcessMailboxCommand;

$application = new Application("Noraktech Ticking System");

$newCommand = new ProcessMailboxCommand();
$newCommand->setConfigs($config);


$application->add($newCommand);


$application->run();