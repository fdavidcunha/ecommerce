<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class Item {

		private $id;
		private $description;
		private $amount;
		private $quantity;

		public function __construct( $id, $description, $amount, $quantity )
		{

			if ( !$id > 0 ) {

				throw new Exception( "ID do item não informado ou inválido!" );
				
			}

			if ( !$description ) {

				throw new Exception( "Descrição do item não informado ou inválido!" );
				
			}

			if ( !$amount || !$amount > 0 ) {

				throw new Exception( "Valor total do item não informado ou inválido!" );
				
			}

			if ( !$quantity || !$quantity > 0 ) {

				throw new Exception( "Quantidade do item não informado ou inválido!" );
				
			}

			$this->id          = $id;
			$this->description = $description;
			$this->amount      = $amount;
			$this->quantity    = $quantity;

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$item = $dom->createElement( "item" );
			$item = $dom->appendChild( $item );

			$amount = $dom->createElement( "amount", number_format( $this->amount, 2, ".", "" ) );
			$amount = $item->appendChild( $amount );

			$id = $dom->createElement( "id", $this->id );
			$id = $item->appendChild( $id );

			$quantity = $dom->createElement( "quantity", $this->quantity );
			$quantity = $item->appendChild( $quantity );

			$description = $dom->createElement( "description", $this->description );
			$description = $item->appendChild( $description );

			return $item;

		}

	}

?>