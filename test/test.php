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
// $users = User::columns(['id', 'firstName', 'lastName'])->where(['firstName' => ['Hans']])->list('id');

// $result = User::columns(['id','password'])->where(['id' => 1])->orWhere(['id' => 2, 'firstName' => 'Lars'])->list();
 $result = User::set(['firstName' => 'Tandy', 'lastName' => 'Libra', 'category' => 'Inger'])->debug()->insert();

Vardump::json(
	$result
	, JSON_PRETTY_PRINT
);
