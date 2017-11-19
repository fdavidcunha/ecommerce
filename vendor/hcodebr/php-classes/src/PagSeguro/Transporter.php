<?php

	namespace Hcode\PagSeguro;

	use \GuzzleHttp\Client;
	use Hcode\Model\Order;

	/**
	* Classe que envia as informações para o pagseguro e recebe o XML de retorno.
	*/

	class Transporter {

		public static function createSession()
		{

			$client = new Client();

			// Obtendo uma sessão com o PagSeguro.
			$res = $client->request( 'POST', Config::getUrlSessions() . "?" . http_build_query( Config::getAuthentication() ), [ 'verify' => false ] );
			
			$xml = simplexml_load_string( $res->getBody()->getContents() );

			// Retornando o ID da sessão com o PagSeguro.
			return ( (string)$xml->id );

		}

		public static function sendTransaction( $payment )
		{

			$client = new Client();

			// Obtendo uma sessão com o PagSeguro.
			$res = $client->request( 'POST', Config::getUrlTransaction() . "?" . http_build_query( Config::getAuthentication() ), [ 
				'verify'  => false,
				'headers' => [
					'Content-Type' => 'application/xml'
				],
				'body' => $payment->getDOMDocument()->saveXml()
			]);
			
			$xml = simplexml_load_string( $res->getBody()->getContents() );

			$order = new Order();
			$order->get( (int)$xml->reference );
			$order->setPagSeguroTransactionResponse(
				(string)$xml->code,
				(float)$xml->grossAmount,
				(float)$xml->disccountAmount,
				(float)$xml->freeAmount,
				(float)$xml->netAmount,
				(float)$xml->extraAmount,
				(string)$xml->paymentLink
			);

			return $xml;

		}

		public static function getNotification( $code, $type ) 
		{

			$url = "";

			switch ( $type ) 
			{
				
				case 'transaction':
					
					$url = Config::getNotificationTransactionURL();

				break;
				
				default:
					
					throw new Exception( "Notificação inválida!" );
					
				break;

			}

			$client = new Client();

			// Solicitanto ao PagSeguro a notificação.
			$res = $client->request( 'GET', $url . $code . "?" . http_build_query( Config::getAuthentication() ), [ 'verify' => false ] );
			
			$xml = simplexml_load_string( $res->getBody()->getContents() );

			$order = new Order();
			$order->get( (int)$xml->reference );

			// Verificando se o status do pedido mudou no PagSeguro para atualizar no banco de dados.
			if ( $order->getidstatus() !== (int)$xml->status ) {

				$order->setidstatus( (int)$xml->status );
				$order->save();

			}

			// Gravando um log.
			$filename = $_SERVER[ 'DOCUMENT_ROOT' ] . DIRECTORY_SEPARATOR . "res" . DIRECTORY_SEPARATOR . "logs" . DIRECTORY_SEPARATOR . date( "YmdHis" ) . ".json";

			$file = fopen( $filename, "a+" );
			
			fwrite( $file, json_encode( [
				'post' => $_POST,
				'xml'  => $xml
			]));

			fclose( $file );

			return $xml;

		}
		
	}
?>