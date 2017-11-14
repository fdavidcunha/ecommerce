<?php
	
	use \Hcode\Page;
	use \Hcode\Model\User;
	use \Hcode\PagSeguro\Config;
	use \Hcode\Model\Order;
	use \Hcode\PagSeguro\Transporter;

	# Rota para o pagamento do pagseguro.
	$app->post( '/payment/credit', function() {

		User::verifyLogin( false );

		$order = new Order();
		$order->getFromSession();

		$address = $order->getAddress();

		$cart = $order->getCart();

		var_dump($order->getValues());
		var_dump($address->getValues());
		var_dump($cart->getValues());

	});

	# Rota para o leiaute de pagamento.
	$app->get( '/payment', function() {

		User::verifyLogin();

		$order = new Order();
		$order->getFromSession();

		$years = [];

		for ( $y = date( 'Y' ); $y < date( 'Y') + 14; $y++ )
		{

			array_push( $years, $y );

		}

		$page = new Page();

		$page->setTpl( "payment", [
			"order"     => $order->getValues(),
			"msgError"  => Order::getError(),
			"years"     => $years,
			"pagseguro" => [
				"urlJS" => Config::getUrlJS(),
				"id"    => Transporter::createSession(),
				"maxInstallmentNoInterest" => Config::MAX_INSTALLMENT_NO_INTEREST,
				"maxInstallment"           => Config::MAX_INSTALLMENT
			]
		]);

	} );

	// Rota para obter uma sessão com o pagseguro. Apenas para testes.
	/*$app->get( '/payment/pagseguro', function() {

		$client = new Client();

		// Obtendo uma sessão com o PagSeguro.
		$res = $client->request( 'POST', Config::getUrlSessions() . "?" . http_build_query( Config::getAuthentication() ), [ 'verify' => false ] );
		
		echo $res->getBody()->getContents();

	} );*/

?>