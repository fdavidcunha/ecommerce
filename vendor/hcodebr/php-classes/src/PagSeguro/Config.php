<?php
	
	namespace Hcode\PagSeguro;

	/**
	* Classe de configuração do PagSeguro.
	*/
	class Config
	{
		
		const SANDBOX             = true;
		const SANDBOX_EMAIL       = 'f.david.cunha@gmail.com';
		const PRODUCTION_EMAIL    = 'f.david.cunha@gmail.com';
		const SANDBOX_TOKEN       = 'E0B0B718A2EA40D3AE599033F57E8384';
		const PRODUCTION_TOKEN    = 'F80BD77AC0FB45E9A252BEC4E9AFA579';
		const SANDBOX_SESSIONS    = 'https://ws.sandbox.pagseguro.uol.com.br/v2/sessions';
		const PRODUCTION_SESSIONS = 'https://ws.pagseguro.uol.com.br/v2/sessions';

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
	}
?>