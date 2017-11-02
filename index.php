<?php 
	session_start();

	require_once("vendor/autoload.php");

	use \Slim\Slim;

	$app = new Slim();

	$app->config('debug', true);

	require_once( "functions.php" );
	require_once( "site.php" );
	require_once( "admin.php" );
	require_once( "admin-users.php" );
	require_once( "admin-categories.php" );
	require_once( "admin-products.php" );

	$app->notFound( function() use ( $app ) {

		http_response_code( 404 );
  		echo file_get_contents( 'res/404.html' );
  		exit;
	});

	$app->notFound( function() use ( $app ) {

		http_response_code( 500 );
  		echo file_get_contents( 'res/404.html' );
  		exit;
	});

	$app->run();
 ?>