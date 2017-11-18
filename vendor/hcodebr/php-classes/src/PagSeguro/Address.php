<?php

	namespace Hcode\PagSeguro;

	use Exception;
	use DOMDocument;
	use DOMElement;

	class Address {

		private $street;
		private $number;
		private $complement;
		private $district;
		private $postalCode;
		private $city;
		private $state;
		private $country;

		public function __construct( $street, $number, $complement, $district, $postalCode, $city, $state, $country )
		{

			if ( !$street ) {

				throw new Exception( "Rua do endereço não informado!" );
				
			}

			if ( !$number ) {

				throw new Exception( "Rua do endereço não informado!" );
				
			}

			if ( !$district ) {

				throw new Exception( "Bairro do endereço não informado!" );
				
			}

			if ( !$postalCode ) {

				throw new Exception( "CEP do endereço não informado!" );
				
			}

			if ( !$city ) {

				throw new Exception( "Cidade do endereço não informado!" );
				
			}

			if ( !$state ) {

				throw new Exception( "Estado do endereço não informado!" );
				
			}

			if ( !$country ) {

				throw new Exception( "País do endereço não informado!" );
				
			}

			$this->street     = $street;
			$this->number     = $number;
			$this->complement = $complement;
			$this->district   = $district;
			$this->postalCode = $postalCode;
			$this->city       = $city;
			$this->state      = $state;
			$this->country    = $country;

		}

		public function getDOMElement( $node = "address" )
		{

			$dom = new DOMDocument();

			$address = $dom->createElement( $node );
			$address = $dom->appendChild( $address );

			$street = $dom->createElement( "street", $this->street );
			$street = $address->appendChild( $street );

			$number = $dom->createElement( "number", $this->number );
			$number = $address->appendChild( $number );

			$complement = $dom->createElement( "complement", $this->complement );
			$complement = $address->appendChild( $complement );

			$district = $dom->createElement( "district", $this->district );
			$district = $address->appendChild( $district );

			$postalCode = $dom->createElement( "postalCode", $this->postalCode );
			$postalCode = $address->appendChild( $postalCode );

			$city = $dom->createElement( "city", $this->city );
			$city = $address->appendChild( $city );

			$state = $dom->createElement( "state", $this->state );
			$state = $address->appendChild( $state );

			$country = $dom->createElement( "country", $this->country );
			$country = $address->appendChild( $country );

			return $address;

		}

	}
	
?>