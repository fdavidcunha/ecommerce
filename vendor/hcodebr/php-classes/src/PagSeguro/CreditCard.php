<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class CreditCard {

		private $token;
		private $installment;
		private $holder;
		private $billingAddress;

		public function __construct( $token, $installment, $holder, $billingAddress )
		{

			if ( !$token ) {

				throw new Exception( "Token do cartão de crédito não informado!" );
				
			}

			$this->token          = $token;
			$this->installment    = $installment;
			$this->holder         = $holder;
			$this->billingAddress = $billingAddress;

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$creditCard = $dom->createElement( "creditCard" );
			$creditCard = $dom->appendChild( $creditCard );

			$token = $dom->createElement( "token", $this->token );
			$token = $creditCard->appendChild( $token );

			// Importando o nó Installment junto com os nós filhos do mesmo.
			$installment = $this->installment->getDOMElement();
			$installment = $dom->importNode( $installment, true );
			$installment = $creditCard->appendChild( $installment );

			// Importando o nó Holder junto com os nós filhos do mesmo.
			$holder = $this->holder->getDOMElement();
			$holder = $dom->importNode( $holder, true );
			$holder = $creditCard->appendChild( $holder );

			// Importando o nó BillingAddress junto com os nós filhos do mesmo.
			$billingAddress = $this->billingAddress->getDOMElement( "billingAddress" );
			$billingAddress = $dom->importNode( $billingAddress, true );
			$billingAddress = $creditCard->appendChild( $billingAddress );

			return $creditCard;

		}

	}

?>