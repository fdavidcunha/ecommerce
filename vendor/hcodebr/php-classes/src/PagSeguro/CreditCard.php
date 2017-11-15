<?php

	namespace \Hcode\PagSeguro;

	class CreditCard {

		const PAC   = 1;
		const SEDEX = 2;
		const OTHER = 3;

		private $token;
		private $installment;
		private $holder;
		private $billingAddress;

	}
?>