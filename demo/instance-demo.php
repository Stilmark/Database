<?php

include_once 'init.php';

use Stilmark\Demo\User;
use Stilmark\Demo\Category;

use Stilmark\Base\Render;

// Get all users without soft deleted
$allUsers = User::getAll(); 

// Return some users grouped by category id - wihtout softdeleted users
$groupedUsers = User::getGrouped(
	column: 'category_id',
	conditions: ['id' => [1,2,3]]
);

// Return users with id 1, 2, 3 - including deleted users
$includingDeletedUsers = User::includeDeleted()->where(['id' => [1,2,3]])->list();

// Return users where firstName is Hans indexed with column id
$columnsWhereUser = User::columns(['id', 'firstName', 'lastName'])->where(['firstName' => ['Hans']])->list('id');

Render::json([
	'allUsers' => $allUsers,
	'groupedUsers' => $groupedUsers,
	'includingDeletedUsers' => $includingDeletedUsers,
	'columnsWhereUser' => $columnsWhereUser
], JSON_PRETTY_PRINT);