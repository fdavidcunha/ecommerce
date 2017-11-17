<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;
	use Hcode\PagSeguro\Payment\Method;

	class Payment {

		private $mode        = "default";
		private $currency    = "BRL";
		private $extraAmount = 0;   // Acréscimo ou desconto ao valor total.
		private $reference   = "";  // Meu número.
		private $items       = [];
		private $sender;
		private $shipping;
		private $method;
		private $creditCard;
		private $bank;

		public function __construct( $reference, $sender, $shipping, $extraAmount = 0 )
		{

			$this->reference   = $reference;
			$this->sender      = $sender;
			$this->shipping    = $shipping;
			$this->extraAmount = $extraAmount;

		}

		public function addItem( $item )
		{

			array_push( $this->items, $item );

		}

		public function setCreditCard( $creditCard )
		{

			$this->creditCard = $creditCard;
			$this->method = Method::CREDIT_CARD;

		}

		public function setBank( $bank )
		{

			$this->bank = $bank;
			$this->method = Method::DEBIT;

		}

		public function setBoleto()
		{

			$this->method = Method::BOLETO;

		}

		public function getDOMDocument()
		{

			// Evitando problema de acentuação. O PagSeguro exige este ISO.
			$dom = new DOMDocument( "1.0", "ISO-8859-1" );

			return $dom;

		}

	}

?>