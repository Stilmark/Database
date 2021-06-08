<?php

namespace Stilmark\Database\tests\units;

// ./vendor/bin/atoum -f /www/stilmark/Database/tests/units/Sqli.php

use atoum;
use Stilmark\Database;
use Stilmark\Parse\Out;
use Symfony\Component\Dotenv\Dotenv;

class Sqli extends atoum
{

	protected function environment()
	{
		$dotenv = new Dotenv();
		$dotenv->load(__DIR__.'/.env');
		return $_ENV;
	}

	public function testFaker()
	{
		$randomName = $this->faker->name;
		echo $randomName;
	}

    /**
     * @dataProvider environment
     */
	public function testConnect()
	{
        $this
        ->given($this->newTestedInstance)
        ->then
            ->object($this->testedInstance->mysqli)
                ->isInstanceOf('\Mysqli');
	}

    /**
     * @dataProvider environment
     */
	public function testCharset() {
        $this
        ->given($this->newTestedInstance)
        ->then
            ->string($this->testedInstance->mysqli->get_charset()->charset)
                ->isEqualTo('utf8');
	}

}