<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model {

	protected $fields = [ "idcategory", "descategory" ];

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select( "select * from tb_products order by desproduct" );
	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select( "CALL sp_products_save( :idproduct, :desproduct, :vlprice, :vlwidth, :vlheight, :vllength, :vlweight, :desurl )", 
                                  array( 
                                  	":idproduct"  => $this->getidproduct(),
                                  	":desproduct" => $this->getdesproduct(),
                                  	":vlprice"    => $this->getvlprice(),
                                  	":vlwidth"    => $this->getvlwidth(),
                                  	":vlheight"   => $this->getvlheight(),
                                  	":vllength"   => $this->getvllength(),
                                  	":vlweight"   => $this->getvlweight(),
	                                ":desurl"     => $this->getdesurl() 
	                            ));

		$this->setData( $results[ 0 ] );

	}

	public function get( $idproduct )
	{

		$sql = new Sql();
		$results = $sql->select( "select * from tb_products where idproduct = :idproduct", [ ':idproduct' => $idproduct ] );

		$this->setData( $results[ 0 ] );

	}

	public function delete()
	{

		$sql = new Sql();
		$sql->query( "delete from tb_products where idproduct = :idproduct", 
			         [ ':idproduct' => $this->getidproduct() ] );

	}

}

 ?>