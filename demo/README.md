## Demo requirements

- PHP
- Mysql

## Installation

Create a new webapp folder and  install with composer

	mkdir webapp
	cd webapp
	composer require stilmark/database

Copy the *demo* and *sql* folders into the webapp folder

	cp -r vendor/stilmark/database/demo .
	cp -r vendor/stilmark/database/demo .

Create an *.env* file in the webapp folder and add your database credentials to the .env file

	DB_HOST=localhost
	DB_DATABASE=webapp
	DB_USERNAME=local
	DB_PASSWORD=local

Create a new database [DB_DATABASE] and grant access to [DB_USERNAME]

Import the 2 sql tables from the sql folder

- category.sql
- users.sql

## Usage

You should now be able to run the demo from the webapp folder using *php*

	php demo/instance-demo.php