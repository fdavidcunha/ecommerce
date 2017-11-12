<?php

	use \Hcode\PageAdmin;
	use \Hcode\Model\User;

	$app->get("/admin/users/:iduser/password", function( $iduser ) {

		User::verifyLogin();

		$user = new User();
		$user->get( (int)$iduser );

		$page = new PageAdmin();
		$page->setTpl( "users-password", [
			"user"       => $user->getValues(),
			'msgError'   => User::getError(),
			'msgSuccess' => User::getSuccess()
		] );
	
	});

	$app->post("/admin/users/:iduser/password", function( $iduser ) {

		User::verifyLogin();

		if ( !isset( $_POST[ 'despassword' ] ) || $_POST[ 'despassword' ] === '' ) {

			User::setError( "Nova senha não informada" );
			header( "location: /admin/users/$iduser/password" );
			exit();

		}	

		if ( !isset( $_POST[ 'despassword-confirm' ] ) || $_POST[ 'despassword-confirm' ] === '' ) {

			User::setError( "Confirmação da nova senha não informada" );
			header( "location: /admin/users/$iduser/password" );
			exit();
			
		}	

		if ( $_POST[ 'despassword' ] !== $_POST[ 'despassword-confirm' ] ) {

			User::setError( "Senha e confirmação de senha não são iguais" );
			header( "location: /admin/users/$iduser/password" );
			exit();

		}

		$user = new User();
		$user->get( (int)$iduser );
		$user->setPassword( User::getPasswordHash( $_POST[ 'despassword' ] ) );

		User::setSuccess( "Senha alterada com sucesso!" );
		header( "location: /admin/users/$iduser/password" );
		exit();
	
	});

	$app->get("/admin/users", function() {

		User::verifyLogin();

		$search = ( isset( $_GET[ 'search' ] ) ) ? $_GET[ 'search' ] : "";
		$page   = ( isset( $_GET[ 'page' ] ) ) ? $_GET[ 'page' ] : 1;

		if ( $search != '' ) {

			$pagination = User::getPageSearch( $search, $page );

		} else {

			$pagination = User::getPage( $page );

		}

		$pages = [];

		for ( $x = 0; $x < $pagination[ 'pages' ]; $x++ )
		{

			array_push( $pages, [
						'href' => '/admin/users?' . http_build_query( [ 
							'page'   => $x + 1,
							'search' => $search
						] ),
						'text' => $x + 1
			 ] );

		}

		$page = new PageAdmin();
		$page->setTpl( "users", [
			'users'  => $pagination[ 'data' ],
			'search' => $search,
			'pages'  => $pages
		] );
	});

	$app->get("/admin/users/create", function() {

		User::verifyLogin();

		$page = new PageAdmin();
		$page->setTpl("users-create");
	});

	$app->get("/admin/users/:iduser/delete", function($iduser) {

		User::verifyLogin();

		$user = new User();
		$user->get( (int)$iduser );
		$user->delete();

		header( "Location: /admin/users" );
		exit;
	});

	$app->get("/admin/users/:iduser", function($iduser) {

		User::verifyLogin();

		$user = new User();
		$user->get( (int)$iduser );

		$page = new PageAdmin();
		$page->setTpl("users-update", array( 'user' => $user->getValues() ) );
	});

	$app->post("/admin/users/create", function() {

		User::verifyLogin();

		// Validando se o valor do campo foi realmente informado.
		$_POST[ "inadmin" ] = ( isset( $_POST[ "inadmin" ]) ) ? 1 : 0;

		$user = new User();
		$user->setData($_POST);
		$user->save();

		header( "Location: /admin/users" );
		exit;
	});

	$app->post("/admin/users/:iduser", function($iduser) {

		User::verifyLogin();

		$_POST[ "inadmin" ] = ( isset( $_POST[ "inadmin" ]) ) ? 1 : 0;

		$user = new User();
		$user->get( (int)$iduser );
		$user->setData( $_POST );
		$user->update();

		// Validando se o valor do campo foi realmente informado.
		header( "Location: /admin/users" );
		exit;
	});

?>