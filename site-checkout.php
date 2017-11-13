<?php

	use \Hcode\Page;
	use \Hcode\Model\Cart;
	use \Hcode\Model\Address;
	use \Hcode\Model\User;
	use \Hcode\Model\Order;
	use \Hcode\Model\OrderStatus;

	$app->get( "/checkout", function(){

		User::verifyLogin( false );

		$address = new Address();
		$cart    = Cart::getFromSession();

		if ( isset( $_GET[ 'zipcode' ] ) ) {

			$_GET[ 'zipcode' ] = $cart->getdeszipcode();
		}

		if ( isset( $_GET[ 'zipcode' ] ) ) {

			$address->loadFromCEP( $_GET[ 'zipcode' ] );

			$cart->setdeszipcode( $_GET[ 'zipcode' ] );
			$cart->save();
			$cart->getCalculateTotal();

		}

		if ( !$address->getdesaddress() )    $address->setdesaddress( '' );
		if ( !$address->getdesnumber() )    $address->setdesnumber( '' );
		if ( !$address->getdescomplement() ) $address->setdescomplement( '' );
		if ( !$address->getdesdistrict() )   $address->setdesdistrict( '' );
		if ( !$address->getdescity() )       $address->setdescity( '' );
		if ( !$address->getdesstate() )      $address->setdesstate( '' );
		if ( !$address->getdescountry() )    $address->setdescountry( '' );
		if ( !$address->getdeszipcode() )    $address->setdeszipcode( '' );

		$page = new Page();
		$page->setTpl( "checkout", [
				'cart'     => $cart->getValues(),
				'address'  => $address->getValues(),
				'products' => $cart->getProducts(),
				'error'    => Address::getMsgError()

		] );

	} );

	# Rota para salvar o endereço de entrega.
	$app->post( "/checkout", function(){

		User::verifyLogin( false );


		if ( !isset( $_POST[ 'zipcode' ] ) || $_POST[ 'zipcode' ] === '' ) {

			Address::setMsgError( "Informe o CEP" );
			header( "location: /checkout" );
			exit();

		}

		if ( !isset( $_POST[ 'desaddress' ] ) || $_POST[ 'desaddress' ] === '' ) {

			Address::setMsgError( "Informe o endereço" );
			header( "location: /checkout" );
			exit();

		}

		if ( !isset( $_POST[ 'desdistrict' ] ) || $_POST[ 'desdistrict' ] === '' ) {

			Address::setMsgError( "Informe o bairro" );
			header( "location: /checkout" );
			exit();

		}

		if ( !isset( $_POST[ 'descity' ] ) || $_POST[ 'descity' ] === '' ) {

			Address::setMsgError( "Informe a cidade" );
			header( "location: /checkout" );
			exit();

		}

		if ( !isset( $_POST[ 'desstate' ] ) || $_POST[ 'desstate' ] === '' ) {

			Address::setMsgError( "Informe o estado" );
			header( "location: /checkout" );
			exit();

		}

		if ( !isset( $_POST[ 'descountry' ] ) || $_POST[ 'descountry' ] === '' ) {

			Address::setMsgError( "Informe o país" );
			header( "location: /checkout" );
			exit();

		}

		$user = User::getFromSession();

		$address = new Address();

		$_POST[ 'deszipcode' ] = $_POST[ 'zipcode' ];
		$_POST[ 'idperson' ]   = $user->getidperson();

		$address->setData( $_POST );
		$address->save();

		$cart = Cart::getFromSession();
		$cart->getCalculateTotal();

		$order = new Order();
		$order->setData( [
			'idcart'    => $cart->getidcart(),
			'idaddress' => $address->getidaddress(),
			'iduser'    => $user->getiduser(),
			'idstatus'  => OrderStatus::AGUARDANDO_PAGAMENTO,
			'vltotal'   => $cart->getvltotal()
		] );

		$order->save();
		$order->toSession();

		/*switch ( (int)$_POST[ 'payment-method' ] ){

			case 1:
				header( "location: /order/".$order->getidorder()."/pagseguro" );
				break;

			case 2:
				header( "location: /order/".$order->getidorder()."/paypal" );
				break;

		}*/

		header( "location: /payment" );
		exit();

	} );

	# Rota para o pagamento.
	$app->get( "/order/:idorder", function( $idorder ) {

		User::verifyLogin( false );
		
		$order = new Order();
		$order->get( (int)$idorder );

		$page = new Page();
		$page->setTpl( "payment", [
			'order' => $order->getValues()
		] );
	} );

	# Rota para o boleto.
	$app->get( "/boleto/:idorder", function( $idorder ) {

		User::verifyLogin( false );

		$order = new Order();
		$order->get( (int)$idorder );

		// DADOS DO BOLETO PARA O SEU CLIENTE
		
		// Dias para pagamento.
		$dias_de_prazo_para_pagamento = 3;
		
		// Taxa de emissão de boleto.
		$taxa_boleto = 5.00;

		// Data de vencimento do título.
		$data_venc = date( "d/m/Y", time() + ( $dias_de_prazo_para_pagamento * 86400 ) );
		
		// Valor - REGRA: Sem pontos na milhar e tanto faz com "." ou "," ou com 1 ou 2 ou sem casa decimal.
		$valor_cobrado = formatPrice( $order->getvltotal() ); 
		$valor_cobrado = str_replace( ".", "", $valor_cobrado );
		$valor_cobrado = str_replace( ",", ".", $valor_cobrado );
		$valor_boleto  = number_format( $valor_cobrado + $taxa_boleto, 2, ',', '' );

		// Nosso numero - REGRA: Máximo de 8 caracteres.
		$dadosboleto[ "nosso_numero" ] = $order->getidorder();  

		// Numero do pedido ou nosso numero.
		$dadosboleto[ "numero_documento" ] = $order->getidorder();	

		// Data de Vencimento do Boleto - REGRA: Formato DD/MM/AAAA.
		$dadosboleto[ "data_vencimento" ] = $data_venc; 

		// Data de emissão do Boleto.
		$dadosboleto[ "data_documento" ] = date( "d/m/Y" ); 

		// Data de processamento do boleto (opcional).
		$dadosboleto[ "data_processamento" ] = date("d/m/Y"); 

		// Valor do Boleto - REGRA: Com vírgula e sempre com duas casas depois da virgula.
		$dadosboleto[ "valor_boleto" ] = $valor_boleto; 	

		// DADOS DO SEU CLIENTE
		
		// Nome do cliente.
		$dadosboleto[ "sacado"] = $order->getdesperson();
		
		// Primeira parte do endereço.
		$dadosboleto[ "endereco1" ] = $order->getdesaddress() . ", " . $order->getdesdistrict();
		
		// Segunda parte do endereço.
		$dadosboleto[ "endereco2" ] =  $order->getdescity() . " - " . 
		                               $order->getdesstate() . " - " . 
		                               $order->getdescountry() . " - CEP: " . 
		                               $order->getdeszipcode();

		// INFORMACOES PARA O CLIENTE
		
		// Mensagens personalizadas.
		$dadosboleto[ "demonstrativo1" ] = "Pagamento de Compra na Loja Hcode E-commerce";
		$dadosboleto[ "demonstrativo2" ] = "Taxa bancária - R$ 0,00";
		$dadosboleto[ "demonstrativo3" ] = "";
		
		// Instruções de pagamento.
		$dadosboleto[ "instrucoes1" ] = "- Sr. Caixa, cobrar multa de 2% após o vencimento";
		$dadosboleto[ "instrucoes2" ] = "- Receber até 10 dias após o vencimento";
		$dadosboleto[ "instrucoes3" ] = "- Em caso de dúvidas entre em contato conosco: suporte@hcode.com.br";
		$dadosboleto[ "instrucoes4" ] = "&nbsp; Emitido pelo sistema Projeto Loja Hcode E-commerce - www.hcode.com.br";

		// DADOS OPCIONAIS DE ACORDO COM O BANCO OU CLIENTE

		$dadosboleto[ "quantidade"]      = "";
		$dadosboleto[ "valor_unitario" ] = "";
		$dadosboleto[ "aceite" ]         = "";		
		$dadosboleto[ "especie" ]        = "R$";
		$dadosboleto[ "especie_doc" ]    = "";

		// ---------------------- DADOS FIXOS DE CONFIGURAÇÃO DO SEU BOLETO --------------- //

		// DADOS DA SUA CONTA - ITAÚ
		
		// Numero da agencia, sem digito.
		$dadosboleto[ "agencia" ]  = "1690"; 

		// Numero da conta, sem digito
		$dadosboleto[ "conta" ] = "48781";	
		
		// Digito do Numero da conta
		$dadosboleto[ "conta_dv" ] = "2"; 	

		// DADOS PERSONALIZADOS - ITAÚ
		
		// Código da Carteira: pode ser 175, 174, 104, 109, 178, ou 157
		$dadosboleto[ "carteira" ] = "175";  

		// SEUS DADOS
		$dadosboleto[ "identificacao"] = "Hcode Treinamentos";
		$dadosboleto[ "cpf_cnpj" ]     = "24.700.731/0001-08";
		$dadosboleto[ "endereco" ]     = "Rua Ademar Saraiva Leão, 234 - Alvarenga, 09853-120";
		$dadosboleto[ "cidade_uf" ]    = "São Bernardo do Campo - SP";
		$dadosboleto[ "cedente" ]      = "HCODE TREINAMENTOS LTDA - ME";

		// NÃO ALTERAR!
		$path = $_SERVER[ 'DOCUMENT_ROOT' ] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "boletophp" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR;
		
		require_once( $path . "funcoes_itau.php" ); 
		require_once( $path . "layout_itau.php" );

	} );

?>