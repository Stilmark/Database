<?php

include_once 'init.php';

use Stilmark\Demo\User;
use Stilmark\Base\Render;

$user = User::set(['category_id' => 'INCREMENT()', 'email' => 'DECREMENT()'])->update(1);

Render::json($user);
