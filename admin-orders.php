<?php

	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Order;
	use \Hcode\Model\OrderStatus;

	# Rota para edição do status do pedido.
	$app->get( "/admin/orders/:idorder/status", function( $idorder ) {

		User::verifyLogin();

		$order = new Order();
		$order->get( (int)$idorder );
		
		$page = new PageAdmin();
		$page->setTpl( "order-status", [
			'order'      => $order->getValues(),
			'status'     => OrderStatus::listAll(),
			'msgError'   => Order::getError(),
			'msgSuccess' => Order::getSuccess()
		] );

	} );

	# Rota para salvar o status do pedido.
	$app->post( "/admin/orders/:idorder/status", function( $idorder ) {

		User::verifyLogin();

		if ( !isset( $_POST[ 'idstatus' ] ) || (int)$_POST[ 'idstatus' ] <= 0 ) {

			Order::setError( "Situação do pedido não informada" );

			header( "location: /admin/orders/" . $idorder . "/status" );
			exit;

		}

		$order = new Order();
		$order->get( (int)$idorder );
		$order->setidstatus( (int)$_POST[ 'idstatus' ] );
		$order->save();
		
		// --------------------------------------------------------------- //
		// --- Enviando e-mail para o usuário com a situação do pedido. ---//
		// --------------------------------------------------------------- //

		// Encriptando um link para recuperação de senha.
		/*$code = User::encrypt_decrypt( 'encrypt', $data_recovery[ "idrecovery" ] );

		if ( $inadmin === true ) {

			$link = "https://temsaboresaude.com.br/admin/forgot/reset?code=$code";

		} else {

			$link = "https://temsaboresaude.com.br/forgot/reset?code=$code";

		}

		$mailer = new Mailer( $data[ "desemail" ], $data[ "desperson" ], utf8_decode( "Redefinição de senha" ), "forgot", 
			                  array( "name" => $data[ "desperson" ],
			                  		 "link" => $link
			                  ));
		$mailer->send();*/

		// --------------------------------------------------------------- //
		// ------------------- Fim do envio de e-mail. --------------------//
		// --------------------------------------------------------------- //

		Order::setSuccess( "Situação do pedido atualizada com sucesso!" );

		header( "location: /admin/orders/" . $idorder . "/status" );
		exit;

	} );

	# Rota para exclusão do pedido.
	$app->get( "/admin/orders/:idorder/delete", function( $idorder ) {

		User::verifyLogin();

		$order = new Order();
		$order->get( (int)$idorder );
		$order->delete();

		header( "location: /admin/orders" );
		exit;

	} );

	# Rota para os detalhes do pedido.
	$app->get( "/admin/orders/:idorder", function( $idorder ) {

		User::verifyLogin();

		$order = new Order();
		$order->get( (int)$idorder );

		$cart = $order->getCart();
		
		$page = new PageAdmin();

		$page->setTpl( "order", [
			'order'    => $order->getValues(),
			'cart'     => $cart->getValues(),
			'products' => $cart->getProducts()
		] );

	} );

	# Essa rota deve ser a última por ser a rota menor. Rotas maiores devem vir primeiro para não sobrescrever as menores.
	$app->get( "/admin/orders", function() {

		User::verifyLogin();

		$search = ( isset( $_GET[ 'search' ] ) ) ? $_GET[ 'search' ] : "";
		$page   = ( isset( $_GET[ 'page' ] ) ) ? $_GET[ 'page' ] : 1;

		if ( $search != '' ) {

			$pagination = Order::getPageSearch( $search, $page );

		} else {

			$pagination = Order::getPage( $page );

		}

		$pages = [];

		for ( $x = 0; $x < $pagination[ 'pages' ]; $x++ )
		{

			array_push( $pages, [
						'href' => '/admin/orders?' . http_build_query( [ 
							'page'   => $x + 1,
							'search' => $search
						] ),
						'text' => $x + 1
			 ] );

		}

		$page = new PageAdmin();
		$page->setTpl( "orders", [
			'orders' => $pagination[ 'data' ],
			'search' => $search,
			'pages'  => $pages
		] );

	} );

?>