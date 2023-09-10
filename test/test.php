<?php

define('ROOT', __DIR__.'/..');
require(ROOT . '/vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;
use Stilmark\Database\Dba;
use Stilmark\Parse\Vardump;

include 'User.php';

$dotenv = new Dotenv();
$dotenv->load(ROOT.'/.env');

/*
$db = new Dba();
$db->table('users');

$user = $db->set(['firstName' => 'Trevor','lastName' => 'Smith'])->debug()->insert();
*/

// $users = User::dryrun();
// $users = User::columns(['id', 'firstName', 'lastName'])->where(['firstName' => ['Hans']])->list('id');

// $result = User::columns(['id','password'])->where(['id' => 1])->orWhere(['id' => 2, 'firstName' => 'Lars'])->debug()->list();
// $result = User::set(['firstName' => 'Tandy', 'lastName' => 'Libra', 'category' => 'Inger']);//->debug()->insert();

$result = User::get(3);

Vardump::json(
	$result
	, JSON_PRETTY_PRINT
);
