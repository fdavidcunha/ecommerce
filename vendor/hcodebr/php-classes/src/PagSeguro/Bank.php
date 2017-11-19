<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class Bank {

		private $name;

		public function __construct( $name )
		{

			if ( !$name ) {

				throw new Exception( "Nome do banco não informado!" );

			}

			$this->name = $name;

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$bank = $dom->createElement( "bank" );
			$bank = $dom->appendChild( $bank );

			$name = $dom->createElement( "name", $this->name );
			$name = $bank->appendChild( $name );

			return $bank;

		}

	}
	
?>