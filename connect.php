<?php

require('vendor/autoload.php');

use Symfony\Component\Dotenv\Dotenv;
use Stilmark\Database\Sqli;

$dotenv = new Dotenv();
$dotenv->load(__DIR__.'/.env');

$sqli= new Sqli();
$users = $sqli->query('UPDATE users SET email = "hans@nicksport.com" WHERE id=3');
echo json_encode($users).PHP_EOL;

$rows = $sqli->affected_rows();
echo 'rows: '.$rows.PHP_EOL;

$info = $sqli->info();
echo json_encode($info).PHP_EOL;