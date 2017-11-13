<?php

	namespace Hcode\PagSeguro;

	use \GuzzleHttp\Client;

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
		
	}
?>