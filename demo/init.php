<?php

ini_set('error_reporting', E_ALL );

define('ROOT', __DIR__.'/..');
require(ROOT . '/vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;

$dotenv = new Dotenv();
$dotenv->load(ROOT.'/.env');