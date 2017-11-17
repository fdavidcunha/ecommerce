<?php

	namespace Hcode\PagSeguro\CreditCard;

	use Exception;
	use DOMDocument;
	use DOMElement;

	/**
	* Classe que armazenas as informações do cliente pagador.
	*/

	class Holder {

		private $name;
		private $cpf;
		private $birthDate;
		private $phone;

		public function __construct( $name, $cpf, $birthDate, $phone )
		{

			if ( !$name ) {

				throw new Exception( "Nome do comprador não informado!" );
				
			}

			$this->name      = $name;
			$this->cpf       = $cpf;
			$this->birthDate = $birthDate;
			$this->phone     = $phone;

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$holder = $dom->createElement( "holder" );
			$holder = $dom->appendChild( $holder );

			$name = $dom->createElement( "name", $this->name );
			$name = $holder->appendChild( $name );

			$birthDate = $dom->createElement( "birthDate", $this->birthDate->format( "d/m/Y" ) );
			$birthDate = $holder->appendChild( $birthDate );

			$documents = $dom->createElement( "documents" );
			$documents = $holder->appendChild( $documents );

			// Importando o nó CPF junto com os nós filhos do mesmo.
			$cpf = $this->cpf->getDOMElement();
			$cpf = $dom->importNode( $cpf, true );
			$cpf = $documents->appendChild( $cpf );

			// Importando o nó Phone junto com os nós filhos do mesmo.
			$phone = $this->phone->getDOMElement();
			$phone = $dom->importNode( $phone, true );
			$phone = $holder->appendChild( $phone );

			return $holder;

		}

	}
	
?>