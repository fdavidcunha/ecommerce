<?php

	use Hcode\Page;
	use Hcode\Model\User;
	use Hcode\Model\Order;
	use Hcode\Model\Cart;

	$app->get( "/profile", function() {

		User::verifyLogin( false );

		$user = User::getFromSession();

		$page = new Page();

		$page->setTpl( "profile", [
			'user'         => $user->getValues(),
			'profileMsg'   => User::getSuccess(),
			'profileError' => User::getError()
		] );

	} );

	$app->post( "/profile", function() {

		User::verifyLogin( false );

		$user = User::getFromSession();

		if ( !isset( $_POST[ 'desperson' ] ) || $_POST[ 'desperson' ] === '' ){

			User::setError( "Preencha o seu nome." );

			header( 'location: /profile' );
			exit();

		}

		if ( !isset( $_POST[ 'desemail' ] ) || $_POST[ 'desemail' ] === '' ){

			User::setError( "Preencha o seu e-mail." );

			header( 'location: /profile' );
			exit();

		}

		if ( $_POST[ 'desemail' ] !== $user->getdesemail() ){

			if ( User::checkLoginExist( $_POST[ 'desemail' ] ) === true ){

				User::setError( "Endereço de e-mail já utilizado por outro usuário." );		

				header( 'location: /profile' );
				exit();

			}
		}

		# Garantindo que o usuário não alterou de alguma forma o conteúdo dos campos abaixo.
		$_POST[ 'iduser' ]      = $user->getiduser();
		$_POST[ 'inadmin' ]     = $user->getinadmin();
		$_POST[ 'despassword' ] = $user->getdespassword();
		$_POST[ 'deslogin' ]    = $_POST[ 'desemail' ];

		$user->setData( $_POST );
		$user->update( false );

		$_SESSION[ User::SESSION ] = $user->getValues();

		User::setSuccess( "Informações atualizadas com sucesso!" );

		header( 'location: /profile' );
		exit();

	} );

	$app->get( "/profile/orders", function() {

		User::verifyLogin( false );

		$user = User::getFromSession();

		$page = new Page();
		$page->setTpl( "profile-orders", [
			'orders' => $user->getOrders()
		] );

	} );

	$app->get( "/profile/orders/:idorder", function( $idorder ) {

		User::verifyLogin( false );

		$order = new Order();
		$order->get( (int)$idorder );

		$cart = new Cart();
		$cart->get( (int)$order->getidcart() );
		$cart->getCalculateTotal();

		$page = new Page();
		$page->setTpl( "profile-orders-detail", [
			'order'    => $order->getValues(),
			'cart'     => $cart->getValues(),
			'products' => $cart->getProducts()
		] );

	} );

	$app->get( "/profile/change-password", function() {

		User::verifyLogin( false );

		$page = new Page();
		$page->setTpl( "profile-change-password", [
			'changePassError'   => User::getError(),
			'changePassSuccess' => User::getSuccess()
		] );

	} );

	$app->post( "/profile/change-password", function() {

		User::verifyLogin( false );

		# Verificando se a senha atual foi informada.
		if ( !isset( $_POST[ 'current_pass' ] ) || $_POST[ 'current_pass' ] === '' ) {

			User::setError( "Informe a senha atual" );

			header( "location: /profile/change-password" );
			exit();
		}

		# Verificando se a nova senha foi informada.
		if ( !isset( $_POST[ 'new_pass' ] ) || $_POST[ 'new_pass' ] === '' ) {

			User::setError( "Informe a nova senha" );

			header( "location: /profile/change-password" );
			exit();
		}

		# Verificando se a confirmação de senha foi informada.
		if ( !isset( $_POST[ 'new_pass_confirm' ] ) || $_POST[ 'new_pass_confirm' ] === '' ) {

			User::setError( "Informe a confirmação de senha" );

			header( "location: /profile/change-password" );
			exit();
		}

		# Verificando se a confirmação de senha foi informada.
		if ( $_POST[ 'current_pass' ] === $_POST[ 'new_pass' ] ) {

			User::setError( "A nova senha deve ser diferente da atual" );

			header( "location: /profile/change-password" );
			exit();
		}

		$user = User::getFromSession();

		# Verificando se a senha atual é a senha real do usuário.
		if ( !password_verify( $_POST[ 'current_pass' ], $user->getdespassword() ) ) {

			User::setError( "Senha atual inválida" );

			header( "location: /profile/change-password" );
			exit();

		}

		$user->setdespassword( $_POST[ 'new_pass' ] );
		$user->update();

		User::setSuccess( "Senha alterada com sucesso!" );

		header( "location: /profile/change-password" );
		exit();

	} );

?>