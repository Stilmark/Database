# Database #

This package contains the class **Sqli()** for querying databases and returning results. In addition a query builder or abstraction layer **Dba()** makes it possible to generate queries with a cleaner code.

## Quick reference ##

Where **Sqli()** accepts an SQL statement as a variable, **Dba()** allows you to build a query programatically using chained methods. The following queries would return the same results.

	$sqli->list( 'SELECT * FROM users' );

	$db->table('users')->list();

## Install using composer ##

	composer require stilmark/database
	
Database connection credentials should be passed using `$_ENV` variables: DB_HOST, DB_DATABASE,  DB_USERNAME, DB_PASSWORD. These can ideally be stored in the project's `.env` file, a sample file `.env-sample` contains the required variables - edit and rename the file.

	DB_HOST=127.0.0.1
	DB_DATABASE=test
	DB_USERNAME=test
	DB_PASSWORD=test

# Usage #

- [Dba Class](https://github.com/Stilmark/Database/wiki/Dba-Class)
- [Dba Class Set Methods](https://github.com/Stilmark/Database/wiki/Dba-Class-Set-Methods)
- [Dba Class Request Methods](https://github.com/Stilmark/Database/wiki/Dba-Class-Request-Methods)

- [Sqli Class](https://github.com/Stilmark/Database/wiki/Sqli-Class)
- [Sqli Class Request Methods](https://github.com/Stilmark/Database/wiki/Sqli-Class-Request-Methods)
- [Sqli Class Query Methods](https://github.com/Stilmark/Database/wiki/Sqli-Class-Query-Methods)
