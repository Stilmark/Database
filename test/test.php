<?php

define('ROOT', __DIR__.'/..');
require(ROOT . '/vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;
use Stilmark\Database\Dba;
use Stilmark\Parse\Vardump;

include 'User.php';

$dotenv = new Dotenv();
$dotenv->load(ROOT.'/.env');

// $users = User::dryrun();
$users = User::columns(['id', 'firstName', 'lastName'])->where(['firstName' => ['Hans']])->list('id');
// die();
// $users = User::listId('id');
// $users = User::values(['category' => 'client'])->where(['id' => 4])->list();

echo Vardump::json(
	$users
	, JSON_PRETTY_PRINT
);
