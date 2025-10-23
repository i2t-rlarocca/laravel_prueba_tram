<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| PDO Fetch Style
	|--------------------------------------------------------------------------
	|
	| By default, database results will be returned as instances of the PHP
	| stdClass object; however, you may desire to retrieve records in an
	| array format for simplicity. Here you can tweak the fetch style.
	|
	*/

	'fetch' => PDO::FETCH_CLASS,

	/*
	|--------------------------------------------------------------------------
	| Default Database Connection Name
	|--------------------------------------------------------------------------
	|
	| Here you may specify which of the database connections below you wish
	| to use as your default connection for all database work. Of course
	| you may use many connections at once using the Database library.
	|
	*/

	'default' => 'mysql',

	/*
	|--------------------------------------------------------------------------
	| Database Connections
	|--------------------------------------------------------------------------
	|
	| Here are each of the database connections setup for your application.
	| Of course, examples of configuring each database platform that is
	| supported by Laravel is shown below to make development simple.
	|
	|
	| All database work in Laravel is done through the PHP PDO facilities
	| so make sure you have the driver for your particular database of
	| choice installed on your machine before you begin development.
	|
	*/

	'connections' => array(

		'sqlite' => array(
			'driver'   => 'sqlite',
			'database' => __DIR__.'/../database/production.sqlite',
			'prefix'   => '',
		),
		'mysql' => array(
			'driver'    => 'mysql',
			'host'      => 'dblxprd',//'181.30.9.179',//
			// 'host'      => '172.20.0.1',//'181.30.9.179',//
			'database'  => 'habilitacion',
			// 'username'  => 'uweb',
			'username'  => 'sugar-cas',
			// 'password'  => '1q2w',
			'password'  => 'sugar-cas_1q2w',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			/*'options' => [
                PDO::ATTR_PERSISTENT => true,
		        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8;",
                PDO::ATTR_EMULATE_PREPARES => true,
		    ],*/
		),
		'suitecrm_cas' => array(
			'driver'    => 'mysql',
			 'host'      => 'dblxprd',//'181.30.9.179',//
			// 'host'      => '172.20.0.1',//'181.30.9.179',//
			'database'  => 'suitecrm_cas',
			// 'username'  => 'uweb',
			'username'  => 'sugar-cas',
			// 'password'  => '1q2w',
			'password'  => 'sugar-cas_1q2w',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			'options' => [
                        // PDO::ATTR_PERSISTENT => true,
		        		// PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                        // PDO::ATTR_EMULATE_PREPARES => true,
		    ],
		),	
		'basica' => array(
			'driver'    => 'mysql',
			 'host'      => 'dblxprd',//'181.30.9.179',//
			// 'host'      => '172.20.0.1',//'181.30.9.179',//
			'database'  => 'basica',
			// 'username'  => 'uweb',
			'username'  => 'sugar-cas',
			// 'password'  => '1q2w',
			'password'  => 'sugar-cas_1q2w',
			'charset'   => 'utf8',
			'collation' => 'utf8_unicode_ci',
			'prefix'    => '',
			'options' => [
		        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
		    ],
		),
		'pgsql' => array(
			'driver'   => 'pgsql',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => '',
			'charset'  => 'utf8',
			'prefix'   => '',
			'schema'   => 'public',
		),

		'sqlsrv' => array(
			'driver'   => 'sqlsrv',
			'host'     => 'localhost',
			'database' => 'database',
			'username' => 'root',
			'password' => '',
			'prefix'   => '',
		),

	),

	/*
	|--------------------------------------------------------------------------
	| Migration Repository Table
	|--------------------------------------------------------------------------
	|
	| This table keeps track of all the migrations that have already run for
	| your application. Using this information, we can determine which of
	| the migrations on disk haven't actually been run in the database.
	|
	*/

	'migrations' => 'migrations',

	/*
	|--------------------------------------------------------------------------
	| Redis Databases
	|--------------------------------------------------------------------------
	|
	| Redis is an open source, fast, and advanced key-value store that also
	| provides a richer set of commands than a typical key-value systems
	| such as APC or Memcached. Laravel makes it easy to dig right in.
	|
	*/

	'redis' => array(

		'cluster' => false,

		'default' => array(
			'host'     => '127.0.0.1',
			'port'     => 6379,
			'database' => 0,
		),

	),

);
