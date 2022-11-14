<?php

define('ROOT', __DIR__.'/..');
require(ROOT . '/vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;
use Stilmark\Database\Dba;
use Stilmark\Parse\Dump;

include 'User.php';

$dotenv = new Dotenv();
$dotenv->load(ROOT.'/.env');

$users = User::list();
// $users = User::values(['category' => 'client'])->where(['id' => 4])->update();

echo Dump::json(
	$users
	, JSON_PRETTY_PRINT
);
