<?php

	namespace \Hcode\PagSeguro;

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

	}
?>