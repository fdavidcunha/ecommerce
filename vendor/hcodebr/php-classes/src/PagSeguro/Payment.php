<?php

	namespace Hcode\PagSeguro;

	class Payment {

		private $mode        = "default";
		private $currency    = "BRL";
		private $extraAmount = 0;  // Acréscimo ou desconto ao valor total.
		private $reference   = ""  // Meu número.
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

		public function getDOMDocument()
		{

			// Evitando problema de acentuação. O PagSeguro exige este ISO.
			$dom = new DOMDocument( "1.0", "ISO-8859-1" );

			return $dom;

		}

	}

?>