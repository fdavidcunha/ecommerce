<?php

	namespace \Hcode\PagSeguro;

	class Item {

		private $id;
		private $description;
		private $amount;
		private $quantity;

		public function __construct( $id, $description, $amount, $quantity )
		{

			if ( !$id > !$id > 0 ) {

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

			$quantity = $dom->createElement( "quantity", $this->quantity );
			$quantity = $item->appendChield( $quantity );

			$description = $dom->createElement( "description", $this->description );
			$description = $item->appendChield( $description );

			return $item;

		}

	}

?>