<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class Shipping {

		const PAC   = 1;
		const SEDEX = 2;
		const OTHER = 3;

		private $address;
		private $type;
		private $cost;
		private $addressRequired;

		public function __construct( $address, $type, $cost, $addressRequired = true )
		{

			if ( $type < 1 || $type > 3 ) {

				throw new Exception( "Tipo de entrega não informado ou inválido!" );
				
			}

			$this->address         = $address;
			$this->type            = $type;
			$this->cost            = $cost;
			$this->addressRequired = $addressRequired;

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$shipping = $dom->createElement( "shipping" );
			$shipping = $dom->appendChild( $shipping );

			// Importando o nó Phone junto com os nós filhos do mesmo.
			$address = $this->address->getDOMElement();
			$address = $dom->importNode( $address, true );
			$address = $shipping->appendChild( $address );

			$cost = $dom->createElement( "cost", number_format( $this->cost, 2, ".", '' ) );
			$cost = $shipping->appendChild( $cost );

			$type = $dom->createElement( "type", $this->type );
			$type = $shipping->appendChild( $type );

			$addressRequired = $dom->createElement( "addressRequired", ($this->addressRequired) ? "true" : "false" );
			$addressRequired = $shipping->appendChild( $addressRequired );

			return $shipping;

		}

	}

?>