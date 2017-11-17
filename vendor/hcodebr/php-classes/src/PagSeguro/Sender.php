<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class Sender {

		private $name;
		private $cpf;
		private $bornDate;
		private $phone;
		private $email;
		private $hash;
		private $ip;

		public function __construct( $name, $cpf, $bornDate, $phone, $email, $hash )
		{

			if ( !$name ) {

				throw new Exception( "Nome não informado!" );
				
			}

			if ( !filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {

				throw new Exception( "E-mail não informado ou no formato inválido!" );
				
			}

			if ( !$hash ) {

				throw new Exception( "Identificação do comprador não informada!" );
				
			}

			$this->name     = $name;
			$this->cpf      = $cpf;
			$this->bornDate = $bornDate;
			$this->phone    = $phone;
			$this->email    = $email;
			$this->hash     = $hash;
			$this->ip       = $_SERVER[ "REMOTE_ADDR" ];

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$sender = $dom->createElement( "sender" );
			$sender = $dom->appendChild( $sender );

			$name = $dom->createElement( "name", $this->name );
			$name = $sender->appendChild( $name );

			$email = $dom->createElement( "email", $this->email );
			$email = $sender->appendChild( $email );

			$bornDate = $dom->createElement( "bornDate", $this->bornDate->format( "d/m/Y" ) );
			$bornDate = $sender->appendChild( $bornDate );

			$documents = $dom->createElement( "documents" );
			$documents = $sender->appendChild( $documents );

			// Importando o nó CPF junto com os nós filhos do mesmo.
			$cpf = $this->cpf->getDOMElement();
			$cpf = $dom->importNode( $cpf, true );
			$cpf = $documents->appendChild( $cpf );

			// Importando o nó Phone junto com os nós filhos do mesmo.
			$phone = $this->phone->getDOMElement();
			$phone = $dom->importNode( $phone, true );
			$phone = $sender->appendChild( $phone );

			$hash = $dom->createElement( "hash", $this->hash );
			$hash = $sender->appendChild( $hash );

			$ip = $dom->createElement( "ip", $this->ip );
			$ip = $sender->appendChild( $ip );

			return $sender;

		}

	}
	
?>