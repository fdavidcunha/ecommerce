<?php

	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Order;

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