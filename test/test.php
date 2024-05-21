<?php

include_once 'init.php';

use Stilmark\Database\Dba;
use Stilmark\Parse\Vardump;

use Stilmark\Test\User;
use Stilmark\Test\Category;

/*
$db = new Dba();
$db->table('users');

$user = $db->set(['firstName' => 'Trevor','lastName' => 'Smith'])->debug()->insert();
*/

// $users = User::columns(['id', 'firstName', 'lastName'])->where(['firstName' => ['Hans']])->list('id');

// $result = User::columns(['id','password'])->where(['id' => 1])->orWhere(['id' => 2, 'firstName' => 'Lars'])->debug()->list();
$result = User::where(['deleted_at : IS' => null])->debug()->list();


// $result = User::set(['firstName' => 'Tandy', 'lastName' => 'Libra', 'category' => 'Inger'])->debug()->insert();

Vardump::json(
	$result
	, JSON_PRETTY_PRINT
);

// $result = User::get(id: 4, with: ['category']);

$result = User::getGrouped(
	column: 'category_id',
	conditions: ['id' => [1,2,3]]
);

Vardump::json(
	$result
	, JSON_PRETTY_PRINT
);
