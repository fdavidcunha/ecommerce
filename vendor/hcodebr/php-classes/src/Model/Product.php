<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class Product extends Model {

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select( "select * from tb_products order by desproduct" );
	}

	public static function checkList( $list )
	{

		# & = indica que será manipulada a posição de memória.
		foreach ( $list as &$row ) {
			
			$p = new Product();
			$p->setData( $row );
			$row = $p->getValues();

		}

		return $list;

	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select( "CALL sp_products_save( :pidproduct, :pdesproduct, :pvlprice, :pvlwidth, :pvlheight, :pvllength, :pvlweight, :pdesurl, :pdesdescription )", 
                                  array( 
                                  	":pidproduct"      => $this->getidproduct(),
                                  	":pdesproduct"     => $this->getdesproduct(),
                                  	":pvlprice"        => $this->getvlprice(),
                                  	":pvlwidth"        => $this->getvlwidth(),
                                  	":pvlheight"       => $this->getvlheight(),
                                  	":pvllength"       => $this->getvllength(),
                                  	":pvlweight"       => $this->getvlweight(),
	                                ":pdesurl"         => $this->getdesurl(), 
	                                ":pdesdescription" => $this->getdesdescription() 
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

		$filename = $_SERVER[ 'DOCUMENT_ROOT' ] . DIRECTORY_SEPARATOR . 
     				"res" . DIRECTORY_SEPARATOR . 
     				"site" . DIRECTORY_SEPARATOR . 
     				"img" . DIRECTORY_SEPARATOR . 
     				"products" . DIRECTORY_SEPARATOR . 
     				$this->getidproduct() . ".jpg";
     	
     	if ( file_exists( $filename ) ) {
        	unlink( $filename );
     	}

	}

	public function checkPhoto()
	{

		if ( file_exists( $_SERVER[ 'DOCUMENT_ROOT' ] .
			              DIRECTORY_SEPARATOR .
			              "res" . DIRECTORY_SEPARATOR .
			              "site" . DIRECTORY_SEPARATOR .
			              "img" . DIRECTORY_SEPARATOR .
			              "products" . DIRECTORY_SEPARATOR .
			              $this->getidproduct() . ".jpg"
		))
		{

			$url = "/res/site/img/products/" . $this->getidproduct() . ".jpg";

		} else {

			$url = "/res/site/img/product.jpg";

		}

		return $this->setdesphoto( $url );
	}

	public function getValues()
	{

		$this->checkPhoto();

		$values = parent::getValues();

		return $values;

	}

	public function setPhoto( $file )
	{

		# Procurando pelo ponto e montando um array com a imagem.
		$extension = explode( '.', $file[ 'name' ] );

		# Informando que a extensão é a última posição do array.
		$extension = end( $extension );

		switch( $extension ) {

			case "jpg":
			case "jpeg":
			$image = imagecreatefromjpeg( $file[ "tmp_name" ] );
			break;

			case "gif":
			$image = imagecreatefromgif( $file[ "tmp_name" ] );
			break;

			case "png":
			$image = imagecreatefrompng( $file[ "tmp_name" ] );
			break;

		}

		$caminho = $_SERVER[ 'DOCUMENT_ROOT' ] .
			       DIRECTORY_SEPARATOR .
			       "res" . DIRECTORY_SEPARATOR .
			       "site" . DIRECTORY_SEPARATOR .
			       "img" . DIRECTORY_SEPARATOR .
			       "products" . DIRECTORY_SEPARATOR .
			       $this->getidproduct() . ".jpg";

		imagejpeg( $image, $caminho );

		imagedestroy( $image );

		$this->checkPhoto();

	}

	public function getFromURL( $desurl )
	{

		$sql = new Sql();
		$rows = $sql->select( "select * from tb_products where desurl = :desurl limit 1", [
			':desurl' => $desurl
		]);

		$this->setData( $rows[ 0 ] );

	}

	public function getCategories()
	{

		$sql = new Sql();

		return $sql->select(     "select *
			                        from tb_categories a
			                  inner join tb_productscategories b
			                          on a.idcategory = b.idcategory
			                       where b.idproduct = :idproduct"
			               ,[ ':idproduct' => $this->getidproduct() ] );

	}

	// Query para paginação.
	public static function getPage( $page = 1, $itensPerPage = 10 )
	{

		$start = ( $page - 1 ) * $itensPerPage;

		$sql = new Sql();

		$results = $sql->select(  "select SQL_CALC_FOUND_ROWS * 
			                         from tb_products
	                           	 order by desproduct
			                        limit $start, $itensPerPage;" );

		$resultTotal = $sql->select( "select FOUND_ROWS() as nrtotal;" );

		return [ 'data'  => $results,
				 'total' => (int)$resultTotal[ 0 ][ "nrtotal" ],
				 'pages' => ceil( (int)$resultTotal[ 0 ][ "nrtotal" ] / $itensPerPage ) ];

	}

	// Query para paginação com busca.
	public static function getPageSearch( $search, $page = 1, $itensPerPage = 10 )
	{

		$start = ( $page - 1 ) * $itensPerPage;

		$sql = new Sql();

		$results = $sql->select(     "select SQL_CALC_FOUND_ROWS * 
			                            from tb_products
			                           where desproduct like :search
	                           		order by desproduct
			                           limit $start, $itensPerPage;", [
			                     	':search' => '%' . $search . '%'
			                      ] );

		$resultTotal = $sql->select( "select FOUND_ROWS() as nrtotal;" );

		return [ 'data'  => $results,
				 'total' => (int)$resultTotal[ 0 ][ "nrtotal" ],
				 'pages' => ceil( (int)$resultTotal[ 0 ][ "nrtotal" ] / $itensPerPage ) ];

	}

}

 ?>