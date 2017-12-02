<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\User;

class Cart extends Model {

	const SESSION       = "Cart";
	const SESSION_ERROR = "CartError";

	public static function getFromSession()
	{

		$cart = new Cart();

		if ( isset( $_SESSION[ Cart::SESSION ][ 'idcart' ] ) && $_SESSION[ Cart::SESSION ][ 'idcart' ] > 0 ) {

			$cart->get( (int)$_SESSION[ Cart::SESSION ][ 'idcart' ] );

		} else {


			$cart->getFromSessionID();

			if ( !(int)$cart->getidcart() > 0 ) {

				$data = [ 'dessessionid' => session_id() ];

				if ( User::checkLogin( false ) ) {

					$user = User::getFromSession();

					$data[ 'iduser' ] = $user->getiduser();
						
				}

				$cart->setData( $data );
				$cart->save();
				$cart->setToSession();				

			}

		}

		return $cart;

	}


	public function setToSession()
	{

		$_SESSION[ Cart::SESSION ] = $this->getValues();

	}

	public function getFromSessionID()
	{

		$sql = new Sql();

		$results = $sql->select( "select * from tb_carts where dessessionid = :dessessionid", [ ':dessessionid' => session_id() ]);

		if ( count( $results ) > 0 ) {
			
			$this->setData( $results[ 0 ] );

		}

	}

	public function get( $idcart )
	{

		$sql = new Sql();

		$results = $sql->select( "select * from tb_carts where idcart = :idcart", [ ':idcart' => $idcart ] );

		if ( count( $results ) > 0 ) {
			
			$this->setData( $results[ 0 ] );

		}

	}

	public function save()
	{

		$sql = new Sql();
		
		$results = $sql->select( "call sp_carts_save ( :idcart, 
			                                           :dessessionid, 
			                                           :iduser, 
			                                           :deszipcode, 
			                                           :vlfreight, 
			                                           :nrdays )", [
			':idcart'       => $this->getidcart(),
			':dessessionid' => $this->getdessessionid(),
			':iduser'       => $this->getiduser(),
			':deszipcode'   => $this->getdeszipcode(),
			':vlfreight'    => $this->getvlfreight(),
			':nrdays'       => $this->getnrdays()
		]);

		if ( count( $results ) > 0 ) {
		
			$this->setData( $results[ 0 ] );

		}

	}

	public function addProduct( Product $product )
	{

		$sql = new Sql();
		$sql->query( "insert into tb_cartsproducts ( idcart, idproduct ) values ( :idcart, :idproduct )", [
			':idcart'   => $this->getidcart(),
			'idproduct' => $product->getidproduct()
		]);

		$this->getCalculateTotal();

	}

	public function removeProduct( Product $product, $all = false )
	{

		$sql = new Sql();

		if ( $all ) {

			$sql->query( "update tb_cartsproducts set dtremoved = now() where idcart = :idcart and idproduct = :idproduct and dtremoved is null", [
				':idcart'    => $this->getidcart(),
				':idproduct' => $product->getidproduct()
			] );

		} else {

			$sql->query( "update tb_cartsproducts set dtremoved = now() where idcart = :idcart and idproduct = :idproduct and dtremoved is null limit 1", [
				':idcart'    => $this->getidcart(),
				':idproduct' => $product->getidproduct()
			] );

		}

		$this->getCalculateTotal();

	}

	public function getProducts() {

		$consulta = "select b.idproduct,
		                    b.desproduct,
		                    b.vlprice,
		                    b.vlwidth,
		                    b.vlheight,
		                    b.vllength,
		                    b.vlweight,
		                    b.desurl,
		                    count( * ) as nrqtd,
		                    sum( b.vlprice ) as vltotal
			           from tb_cartsproducts a 
			     inner join tb_products b on a.idproduct = b.idproduct 
			          where a.idcart = :idcart 
			            and a.dtremoved is null 
			       group by b.idproduct,
		                    b.desproduct,
		                    b.vlprice,
		                    b.vlwidth,
		                    b.vlheight,
		                    b.vllength,
		                    b.vlweight,
		                    b.desurl
		           order by b.desproduct";

		$sql = new Sql();
		
		$retorno = $sql->select( $consulta, [ ':idcart' => $this->getidcart() ] );

		return Product::checkList( $retorno );

	}

	public function getProductsTotals()
	{

		$consulta =     "select sum( vlprice ) as vlprice,
		                        sum( vlwidth ) as vlwidth,
		                        sum( vlheight ) as vlheight,
		                        sum( vllength ) as vllength,
   		                        sum( vlweight ) as vlweight,
		                        count( * ) as nrqtd
		                   from tb_products a
		             inner join tb_cartsproducts b on a.idproduct = b.idproduct
		                  where b.idcart = :idcart and dtremoved is null";

		$sql = new Sql();

		$results = $sql->select( $consulta, [ 'idcart' => $this->getidcart() ] );

		if ( count( $results ) > 0 ) {

			return $results[ 0 ];

		} else {

			return [];

		}

	}

	public function setFreight( $zipcode )
	{

		$zipcode = str_replace( '-', '', $zipcode );

		$totals = $this->getProductsTotals();

		if ( $totals[ 'nrqtd' ] > 0 ){

			# Altura mínima exigida pelos correios: 2cm;
			if ( $totals[ 'vlheight' ] < 2 ) $totals[ 'vlheight' ] = 2;
			
			# Cumprimento mínimo exigido pelos correios: 16cm.
			if ( $totals[ 'vllength' ] < 16 ) $totals[ 'vllength' ] = 16;

			# Largura mínima exigida pelos correios: 11cm.
			if ( $totals[ 'vlwidth' ] < 11 ) $totals[ 'vlwidth' ] = 11;

			# Valor declarado mínimo exigido pelos correios: R$ 18.00.
			if ( $totals[ 'vlprice' ] < 18 ) $totals[ 'vlprice' ] = 18;

			$qs = http_build_query( [
				'nCdEmpresa'          => '',
				'sDsSenha'            => '',
				'nCdServico'          => '40010',
				'sCepOrigem'          => '50790000',
				'sCepDestino'         => $zipcode,
				'nVlPeso'             => $totals[ 'vlweight' ],
				'nCdFormato'          => '1',
				'nVlComprimento'      => $totals[ 'vllength' ],
				'nVlAltura'           => $totals[ 'vlheight' ],
				'nVlLargura'          => $totals[ 'vlwidth' ],
				'nVlDiametro'         => '0',
				'sCdMaoPropria'       => 'S',
				'nVlValorDeclarado'   => $totals[ 'vlprice' ],
				'sCdAvisoRecebimento' => 'S'

			] );

			$xml = simplexml_load_file( "http://ws.correios.com.br/calculador/CalcPrecoPrazo.asmx/CalcPrecoPrazo?" . $qs );

			$result = $xml->Servicos->cServico;

			if ( $result->MsgErro != '' ) {

				Cart::setMsgError( $result->MsgErro );

			} else {

				Cart::clearMsgError();

			}

			$this->setnrdays( $result->PrazoEntrega );
			$this->setvlfreight( Cart::formatValueToDecimal( $result->Valor ) );
			$this->setdeszipcode( $zipcode );
			$this->save();

			return $result;

		} else {

			
		}

	}

	public static function formatValueToDecimal( $value ) 
	{

		$value = str_replace( '.', '', $value );
		return str_replace( ',', '.', $value );

	}

	public static function setMsgError( $msg )
	{

		$_SESSION[ Cart::SESSION_ERROR ] = $msg;

	}

	public static function getMsgError()
	{

		$msg = ( isset( $_SESSION[ Cart::SESSION_ERROR ] ) ) ? $_SESSION[ Cart::SESSION_ERROR ] : "";

		Cart::clearMsgError();

		return $msg;

	}

	public static function clearMsgError()
	{

		$_SESSION[ Cart::SESSION_ERROR ] = NULL;
	}

	public function updateFreight()
	{

		if ( $this->getdeszipcode() != '' ) {

			$this->setFreight( $this->getdeszipcode() );

		}

	}

	public function getValues()
	{

		$this->getCalculateTotal();

		return parent::getValues();

	}

	public function getCalculateTotal()
	{

		$this->updateFreight();

		$totals = $this->getProductsTotals();

		$products = $this->getProducts();

		if ( count( $products ) == 0 ) {
		     $this->setnrdays( NULL );
		     $this->setvlfreight( 0 );
		}

		$this->setvlsubtotal( $totals[ 'vlprice' ] );
		$this->setvltotal( $totals[ 'vlprice' ] + $this->getvlfreight() );

	}

	public static function removeToSession()
	{
	   
	   $_SESSION[ Cart::SESSION ] = NULL;

	}

}

 ?>