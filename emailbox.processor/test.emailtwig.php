#!/usr/bin/env php
<?php
// application.php

require 'vendor/autoload.php';		

use Twig\Environment;
use Twig\Loader\FilesystemLoader;

$loader = new FilesystemLoader('views');
$twig = new Environment($loader);

echo $twig->render('email-subject.twig', ['emailSubject' => 'Please help', 
    'ticketNumber' => '# 23']);

echo $twig->render('email-subject.twig', ['emailSubject' => 'TMZ',
		'ticketNumber' => '# 32']);