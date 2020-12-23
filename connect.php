<?php

require('vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;
use Stilmark\Database\Sqli;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$sqli= new Sqli();
$users = $sqli->row('SELECT * FROM users');

echo json_encode($users).PHP_EOL;