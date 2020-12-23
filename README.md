# Sqli() #

**Sqli** connection credentials should be passed using $_ENV variables: DB_HOST, DB_DATABASE,  DB_USERNAME, DB_PASSWORD

	require('vendor/autoload.php');

	use Symfony\Component\Dotenv\Dotenv;
	use Stilmark\Database\Sqli;

	$sqli= new Sqli();

