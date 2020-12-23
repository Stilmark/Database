# Sqli() #

**Sqli** connection credentials should be passed using `$_ENV` variables: DB_HOST, DB_DATABASE,  DB_USERNAME, DB_PASSWORD. These can be stored in the projects `.env` file, a sample file `.env-sample` contains the required variables - edit and rename the file.

	require('vendor/autoload.php');

	use Symfony\Component\Dotenv\Dotenv;
	use Stilmark\Database\Sqli;

	$sqli = new Sqli();

## Sqli methods ##

You can request and query the database using SQL statements and format the output using these methods.

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

### listId( $sql [, $key ] ) ###

Request multiple rows indexed by key value.

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

### groupId( $sql [, $key ] ) ###

Request multiple rows indexed by key value.

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

### keys() ###

Rquest the keys contained in the query.

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
