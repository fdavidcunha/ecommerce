<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Model\User;

class Cart extends Model {

	const SESSION = "Cart";

	public static function getFromSession()
	{

		$cart = new Cart();

		if ( isset( $_SESSION[ Cart::SESSION ] ) && $_SESSION[ Cart::SESSION ][ 'idcart' ] > 0 ) {

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
			                                           :derzipcode, 
			                                           :vlfreight, 
			                                           :nrdays)", [
			':idcart'       => $this->getidcart(),
			':dessessionid' => $this->getdessessionid(),
			':iduser'       => $this->getiduser(),
			':derzipcode'   => $this->getderzipcode(),
			':vlfreight'    => $this->getvlfreight(),
			':nrdays'       => $this->getnrdays()
		]);

		$this->setData( $results[ 0 ] );

	}

	public function addProduct( Product $product )
	{

		$sql = new Sql();
		$sql->query( "insert into tb_cartsproducts ( idcart, idproduct ) values ( :idcart, :idproduct", [
			':idcart' =>$this->getidcart(),
			'idproduct' => $product.getidproduct()
		] )

	}

	public function removeProduct( Product $product, $all = false )
	{

		$sql = new Sql();

		if ( $all ) {

			$sql->query( "update tb_cartsproducts set dtremoved = now() where idcart = :idcart and idproduct = :idproduct and dtremoved is null", [
				':idcart' => $this->getidcart(),
				':idproduct' => $product->getidproduct();
			] );

		} else {

			$sql->query( "update tb_cartsproducts set dtremoved = now() where idcart = :idcart and idproduct = :idproduct and dtremoved is null limit 1", [
				':idcart' => $this->getidcart(),
				':idproduct' => $product->getidproduct();
			] );

		}

	}

}

 ?>