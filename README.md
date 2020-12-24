# Database #

This package contains the class **Sqli()** for querying databases and returning results. In addition a query builder or abstraction layer **Dba()** makes it possible to generate queries with a cleaner code.

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

## Dba() ##

Use the Dba() class to build database queries programatically.

	use Stilmark\Database\Dba;
	
	$db = new Dba();

## Dba Set Methods ##

### table( *string* ) ###

Set the name of the table to query.

	$db->table('users');

### columns( *string* | *array* ) ###

Select which columns to return. Can be a single string or an array of strings.

	$db->columns('email');
	
	$db->columns(['id', 'email']);

## Dba Request Methods ##

You can request rows from the database by building queries programatically and format the output using these methods.

### row() ###

Request a single row.

	$user = $db->table('users')->row();
	
Result:

```json
{
  "id": "1",
  "firstName": "Hans",
  "lastName": "Gruber",
  "email": "hans@gruber.com"
}
```


## Sqli() ##

Use the Sqli() class to execute SQL statements.

	use Stilmark\Database\Sqli;
	
	$sqli = new Sqli();

## Sqli Request Methods ##

You can request rows from the database using SQL statements and format the output using these methods.

### row( $sql ) ###

Request a single row.

	$user = $sqli->row('SELECT * FROM users');
	
Result:

```json
{
  "id": "1",
  "firstName": "Hans",
  "lastName": "Gruber",
  "email": "hans@gruber.com"
}
```

### list( $sql ) ###

Request multiple rows.

	$users = $sqli->list('SELECT * FROM users');
	
Result:

```json
[
  {
    "id": "1",
    "firstName": "Hans",
    "lastName": "Gruber",
    "email": "hans@gruber.com"
  },
  {
    "id": "2",
    "firstName": "Lars",
    "lastName": "Ulrich",
    "email": "lars@metal.com"
  },
  {
    "id": "3",
    "firstName": "Hans",
    "lastName": "Nickerdorph",
    "email": "hans@nick.com"
  }
]
```

### listId( $sql [, $key = 'id' ] ) ###

Request multiple rows indexed by key value. The default key is `id`.

	$users = $sqli->listId('SELECT * FROM users LIMIT 2');

A specific key can also be supplied.

	$users = $sqli->listId('SELECT * FROM users LIMIT 2', 'email');

Result:

```json
{
  "hans@gruber.com": {
    "id": "1",
    "firstName": "Hans",
    "lastName": "Gruber",
    "email": "hans@gruber.com"
  },
  "lars@metal.com": {
    "id": "2",
    "firstName": "Lars",
    "lastName": "Ulrich",
    "email": "lars@metal.com"
  }
}
```

### groupId( $sql [, $key = 'id' ] ) ###

Request multiple rows indexed by key value. The default key is `id`.

	$users = $sqli->groupId('SELECT * FROM users');

A specific key can also be supplied.

	$users = $sqli->groupId('SELECT * FROM users', 'firstName');

Result:

```json
{
  "Hans": [
    {
      "id": "1",
      "firstName": "Hans",
      "lastName": "Gruber",
      "email": "hans@gruber.com"
    },
    {
      "id": "3",
      "firstName": "Hans",
      "lastName": "Nickerdorph",
      "email": "hans@nick.com"
    }
  ],
  "Lars": [
    {
      "id": "2",
      "firstName": "Lars",
      "lastName": "Ulrich",
      "email": "lars@metal.com"
    }
  ]
}
```

### keys( $sql ) ###

Request the keys contained in the query.

	$user = $sqli->keys('SELECT * FROM users');
	
Result:

```json
[
  "id",
  "firstName",
  "lastName",
  "email"
]
```

### values( $sql ) ###

Request only the values of the first row in the query.

	$user = $sqli->values('SELECT * FROM users');
	
Result:

```json
[
  "1",
  "Hans",
  "Gruber",
  "hans@gruber.com"
]
```

## Sqli Query Methods ##

You can query the database using SQL statements using these methods.

### query( $sql ) ###

Execute the query. Returns true if the query was successful or reversely false.

	$user = $sqli->query('UPDATE users SET email = "hans@nickol.com" WHERE id=3');
	
Result:

	true

### affected_rows() ###

Return the number of rows changed with the latest query.

	$affected_rows = $sqli->affected_rows();
	
Result:

	1
	
### info() ###

Return info about the latest query.

	$info = $sqli->info();

Result:

	"Rows matched: 1  Changed: 1  Warnings: 0"
