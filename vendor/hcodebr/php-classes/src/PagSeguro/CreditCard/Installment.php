<?php

	namespace Hcode\PagSeguro\CreditCard;

	use Exception;
	use DOMDocument;
	use DOMElement;
	use Hcode\PagSeguro\Config;

	/**
	* Classe que armazenas as informações das parcelas do pagamento.
	*/

	class Installment {

		private $quantity;
		private $value;
		
		public function __construct( $quantity, $value )
		{

			if ( $quantity < 1 || $quantity > Config::MAX_INSTALLMENT ) {

				throw new Exception( "Número de parcelas não informado ou inválido!" );
				
			}

			if ( $value <= 0 ) {

				throw new Exception( "Valor total não informado ou inválido!" );
				
			}

			$this->quantity = $quantity;
			$this->value    = $value;

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$installment = $dom->createElement( "installment" );
			$installment = $dom->appendChild( $installment );

			$value = $dom->createElement( "value", number_format( $this->value, 2, ".", '' ) );
			$value = $installment->appendChild( $value );

			$quantity = $dom->createElement( "quantity", $this->quantity );
			$quantity = $installment->appendChild( $quantity );

			// Por padrão, só uma parcela, já é considerada sem juros. Quando só existe uma parcela não é necessário enviar o campo abaixo.
			$noInterestInstallmentQuantity = $dom->createElement( "noInterestInstallmentQuantity", Config::MAX_INSTALLMENT_NO_INTEREST );
			$noInterestInstallmentQuantity = $installment->appendChild( $noInterestInstallmentQuantity );

			return $installment;

		}

	}

?>