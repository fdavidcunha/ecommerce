<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class Document {

		const CPF = "CPF";

		private $type;
		private $value;

		public function __construct( $type, $value )
		{

			if ( !$value ) {

				throw new Exception( "Documento não informado!" );
				
			}

			switch ( $type ) {
				case  Document::CPF:
					
					if ( !Document::isValidCPF( $value ) ) {

						throw new Exception( "CPF inválido!" );
								
					}

					break;
				
			}

			$this->type  = $type;
			$this->value = $value;

		}

		public static function isValidCPF( $number )
		{

		    $number = preg_replace( '/[^0-9]/', '', (string) $number );

		    if ( strlen( $number ) != 11 )
		        return false;

		    for ( $i = 0, $j = 10, $sum = 0; $i < 9; $i++, $j-- )
		        $sum += $number{ $i } * $j;

		    $rest = $sum % 11;

		    if ( $number{ 9 } != ( $rest < 2 ? 0 : 11 - $rest ) )
		        return false;

		    for ( $i = 0, $j = 11, $sum = 0; $i < 10; $i++, $j-- )
		        $sum += $number{ $i } * $j;

		    $rest = $sum % 11;

		    return ( $number{ 10 } == ( $rest < 2 ? 0 : 11 - $rest ) );

		}

		public function getDOMElement()
		{

			$dom = new DOMDocument();

			$document = $dom->createElement( "document" );
			$document = $dom->appendChild( $document );

			$type = $dom->createElement( "type", $this->type );
			$type = $document->appendChild( $type );

			$value = $dom->createElement( "value", $this->value );
			$value = $document->appendChild( $value );

			return $document;

		}

	}
	
?>