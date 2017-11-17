<?php
	
	namespace Hcode\PagSeguro;

	/**
	* Classe de configuração do PagSeguro.
	*/
	class Config
	{
		
		const SANDBOX             = true;
		const SANDBOX_EMAIL       = "f.david.cunha@gmail.com";
		const SANDBOX_TOKEN       = "E0B0B718A2EA40D3AE599033F57E8384";
		const SANDBOX_SESSIONS    = 'https://ws.sandbox.pagseguro.uol.com.br/v2/sessions';
		const SANDBOX_URL_JS      = 'https://stc.sandbox.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
		const PRODUCTION_EMAIL    = 'f.david.cunha@gmail.com';
		const PRODUCTION_TOKEN    = 'F80BD77AC0FB45E9A252BEC4E9AFA579';
		const PRODUCTION_SESSIONS = 'https://ws.pagseguro.uol.com.br/v2/sessions';
		const PRODUCTION_URL_JS   = 'https://stc.pagseguro.uol.com.br/pagseguro/api/v2/checkout/pagseguro.directpayment.js';
		
		// Máximo de parcelas sem juros.
		const MAX_INSTALLMENT_NO_INTEREST = 1;

		// Máximo de parcelas.
		const MAX_INSTALLMENT = 1;

		// URL de retorno que será chamada quando o pagseguro der o retorno.
		const NOTIFICATION_URL = "http://www.temsaboresaude.com.br/payment/notification";

		public static function getAuthentication()
		{
			
			if ( Config::SANDBOX === true )
			{

				return [
					"email"	=> Config::SANDBOX_EMAIL,
					"token" => Config::SANDBOX_TOKEN
				];
				
			} else {

				return [
					"email"	=> Config::PRODUCTION_EMAIL,
					"token" => Config::PRODUCTION_TOKEN
				];

			}

		}

		public static function getUrlSessions()
		{

			return ( Config::SANDBOX === true ) ? Config::SANDBOX_SESSIONS : Config::PRODUCTION_SESSIONS;

		}

		public static function getUrlJS()
		{

			return ( Config::SANDBOX === true ) ? Config::SANDBOX_URL_JS : Config::PRODUCTION_URL_JS;

		}

	}
?>