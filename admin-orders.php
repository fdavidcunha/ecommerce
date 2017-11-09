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

		$page = new PageAdmin();
		$page->setTpl( "orders", [
			'orders' => Order::listAll()
		] );

	} );


?>