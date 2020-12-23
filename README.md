# Sqli() #

**Sqli** connection credentials should be passed using `$_ENV` variables: DB_HOST, DB_DATABASE,  DB_USERNAME, DB_PASSWORD. These can be stored in the projects `.env` file, a sample file `.env-sample` contains the required variables - edit and rename the file.

	require('vendor/autoload.php');

	use Symfony\Component\Dotenv\Dotenv;
	use Stilmark\Database\Sqli;

	$sqli = new Sqli();

## Sqli methods ##

You can request and query the database using SQL statements and format the output using these methods.

### row() ###

Request a single row.

	$user = $sqli->row('SELECT * FROM users');
	
Result:

```json
{"id":"1","firstName":"Hans","lastName":"Gruber","email":"hans@gruber.com"}
```


### keys() ###

Rquest the keys contained in the query.

	$user = $sqli->keys('SELECT * FROM users');
	
Result:

```json
["id","firstName","lastName","email"]
```
