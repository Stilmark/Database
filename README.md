<div align="center">

# Database abstraction for PHP
MySQLi functions, Query builder and Model instance.
    
[![CodeFactor](https://www.codefactor.io/repository/github/stilmark/database/badge)](https://www.codefactor.io/repository/github/stilmark/database)
    
</div>

## Install using composer ##

    composer require stilmark/database

## Basic usage ##

    require('/vendor/autoload.php');

    use Symfony\Component\Dotenv\Dotenv;
    use Stilmark\Database\Dba;

    $dotenv = new Dotenv();
    $dotenv->load(ROOT.'/.env');

    # Build sql queries
    $users = Dba::instance()->table('users')->list();

    # Extend your models on Dbi()
    $users = User::list();

## :blue_book: Documentation ##

https://stilmark-projects.gitbook.io/database

