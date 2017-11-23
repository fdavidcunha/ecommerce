<?php 
    error_reporting( -1 );
    ini_set( 'display_errors', 1 );

	session_start();

	require_once("vendor/autoload.php");

	use \Slim\Slim;
	use \Slim\App;

	$app = new Slim();

	$app->config('debug', true);

	require_once( "functions.php" );
	require_once( "site.php" );
	require_once( "site-cart.php" );
	require_once( "site-checkout.php" );
	require_once( "site-login.php" );
	require_once( "site-profile.php" );
	require_once( "site-pagseguro.php" );
	require_once( "admin.php" );
	require_once( "admin-users.php" );
	require_once( "admin-categories.php" );
	require_once( "admin-products.php" );
	require_once( "admin-orders.php" );

	$app->notFound( function() use ( $app ) {

		http_response_code( 404 );
  		echo file_get_contents( 'res/404.html' );
  		exit;
	});

    $app->error( function ( \Exception $e ) use ( $app ) {
        
        //$app->render( 'res/500.html');
        echo $e->getMessage();
        echo "<br>";
        echo file_get_contents( 'res/404.html' );
        exit;
    });

	/*$app_ = new \Slim\App();

	// get the app's di-container
	$c = $app_->getContainer();
	$c[ 'phpErrorHandler' ] = function ($c) {
    	return function( $request, $response, $error ) use ( $c ) {
        	return $c[ 'response' ]
            	->withStatus( 500 )
            	->withHeader( 'Content-Type', 'text/html' )
            	->write( 'Vish... algo deu errado!' );
    	};
	};*/

	$app->run();
 ?>