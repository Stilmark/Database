# Database #

This package contains the class **Sqli()** for querying databases and returning results. In addition a query builder or abstraction layer **Dba()** makes it possible to generate queries with a cleaner code. **Dbi()** provides a shorthand to Dba() as a static function.

## Quick reference ##

Where **Sqli()** accepts an SQL statement as a variable, **Dba()** allows you to build a query programatically using chained methods. The following queries would return the same results.

	$sqli = new Sqli();
	$sqli->list( 'SELECT * FROM users' );

	$db = new Dba();
	$db->table('users')->list();

The shorthand database instance **Dbi()** can be used as a static function like this:

	Dbi::table('users')->list();

Extend your object models on Dbi().

	class User extends Dbi {

	    public static $table = 'users';

	}

	$user = User::row(2);

## Install using composer ##

	composer require stilmark/database
	
# Usage #

- [Configure](https://github.com/Stilmark/Database/wiki/Configure)

- [Dbi Class](https://github.com/Stilmark/Database/wiki/Dbi-Class)

- [Dba Class](https://github.com/Stilmark/Database/wiki/Dba-Class)
- [Dba Class Set Methods](https://github.com/Stilmark/Database/wiki/Dba-Class-Set-Methods)
- [Dba Class Request Methods](https://github.com/Stilmark/Database/wiki/Dba-Class-Request-Methods)
- [Dba Class Put Methods](https://github.com/Stilmark/Database/wiki/Dba-Class-Put-Methods)

- [Sqli Class](https://github.com/Stilmark/Database/wiki/Sqli-Class)
- [Sqli Class Request Methods](https://github.com/Stilmark/Database/wiki/Sqli-Class-Request-Methods)
- [Sqli Class Query Methods](https://github.com/Stilmark/Database/wiki/Sqli-Class-Query-Methods)
