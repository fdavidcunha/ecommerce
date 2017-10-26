<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {

	const SESSION = "User";
	const SECRET  = "HcodePhp7_Secret";

	public static function login($login, $password)
	{

		$db = new Sql();

		$results = $db->select("SELECT * FROM tb_users WHERE deslogin = :LOGIN", array(":LOGIN"=>$login));

		if (count($results) === 0) {
			throw new \Exception("Não foi possível fazer login.");
		}

		$data = $results[0];

		if (password_verify($password, $data["despassword"])) {

			$user = new User();
			
			// Criando os métodos gets e sets do objeto, dinamicamente.
			$user->setData($data);

			$_SESSION[User::SESSION] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("Não foi possível fazer login.");
		}
	}

	public static function logout()
	{
		// Excluindo a sessão.
		$_SESSION[User::SESSION] = NULL;
	}

	public static function verifyLogin($inadmin = true)
	{
		
		if (
			!isset( $_SESSION[ User::SESSION ] ) || 
			!$_SESSION[ User::SESSION ] ||
			!(int)$_SESSION[ User::SESSION ][ "iduser" ] > 0 ||
			(bool)$_SESSION[ User::SESSION ][ "inadmin" ] !== $inadmin ) 
		{
			header("Location:/admin/login");
			exit;
		}
	}

	public static function listAll()
	{
		$sql = new Sql();
		return $sql->select( "select * from tb_users u inner join tb_persons p using(idperson) order by p.desperson" );
	}

	public function save()
	{

		$sql = new Sql();

		$results = $sql->select( "CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", 
                                  array( 
                                  	":desperson"   => $this->getdesperson(),
	                                ":deslogin"    => $this->getdeslogin(),
	                                ":despassword" => password_hash( $this->getdespassword(), PASSWORD_DEFAULT, [ "cost" => 12 ] ),
	                                ":desemail"    => $this->getdesemail(),
	                                ":nrphone"     => $this->getnrphone(),
	                                ":inadmin"     => $this->getinadmin() ) 
	                           );

		$this->setData( $results[ 0 ] );
	}

	public function get( $iduser )
	{

		$sql = new Sql();
		$results = $sql->select( "select * from tb_users a inner join tb_persons b using(idperson) where a.iduser = :iduser", array( ':iduser' => $iduser ) );

		$this->setData( $results[ 0 ] );

	}

	public function update()
	{

		$sql = new Sql();
		$results = $sql->select( "CALL sp_usersupdate_save(:iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", 
                                  array( 
                                  	":iduser" => $this->getiduser(),
                                  	":desperson" => $this->getdesperson(),
	                                ":deslogin" => $this->getdeslogin(),
	                                ":despassword" => $this->getdespassword(),
	                                ":desemail" => $this->getdesemail(),
	                                ":nrphone" => $this->getnrphone(),
	                                ":inadmin" => $this->getinadmin() ) 
	                           );

		$this->setData( $results[ 0 ] );
	}


	public function delete()
	{

		$sql = new Sql();
		$sql->query( "CALL sp_users_delete( :iduser )", array( ':iduser' => $this->getiduser() ) );

	}

	public static function getForgot( $email ) 
	{
		$comando = "select * from tb_persons a inner join tb_users b using( idperson ) where a.desemail = :email";

		$sql = new Sql();
	    $results = $sql->select( $comando, array( ':email' => $email ) );

		if ( count( $results ) === 0 )
		{
			throw new \Exception( "Não foi possível recuperar a senha! (01)" );
		}
		else
		{

			$data = $results[ 0 ];

			// Criando um registro de recuperação de senha no banco de dados.
			$retorno = $sql->select("CALL sp_userspasswordsrecoveries_create(:iduser, :desip)", 
					  			 	array( ":iduser" => $data[ "iduser" ], 
							   			   ":desip"  => $_SERVER[ "REMOTE_ADDR" ] 
			));


			if ( count( $retorno ) === 0 )
			{
				// Se não localizou o usuário não permite recuperação de senha.
				throw new \Exception( "Não foi possível recuperar a senha! (02)" );
			}
			else
			{
				$data_recovery = $retorno[ 0 ];

				// Encriptando um link para recuperação de senha.
				$code = User::encrypt_decrypt( 'encrypt', $data_recovery[ "idrecovery" ] );

				$link = "http://www.hcodecommerce.com.br/admin/forgot/reset?code=$code";

				$mailer = new Mailer( $data[ "desemail" ], $data[ "desperson" ], "Redefinição de senha", "forgot", 
					                  array( "name" => $data[ "desperson" ],
					                  		 "link" => $link
					                  ));
				$mailer->send();

				return $data;
			}
		}
	}

	public static function encrypt_decrypt( $action, $string ) 
	{
	    $output         = false;
	    $encrypt_method = "AES-256-CBC";
	    $secret_key     = 'This is my secret key';
	    $secret_iv      = 'This is my secret iv';
	    
	    // hash
	    $key = hash( 'sha256', $secret_key );
	    
	    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
	    $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );
	    
	    if ( $action == 'encrypt' ) 
	    {
	        
	        $output = openssl_encrypt( $string, $encrypt_method, $key, 0, $iv );
	        $output = base64_encode( $output );

	    } else if( $action == 'decrypt' ) 
	    {
	        
	        $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );

	    }

	    return $output;
	}

	public static function validForgotDecrypt( $code )
	{

		$idrecovery = User::encrypt_decrypt( 'decrypt', $code );

		$sql = new Sql();
		$results = $sql->select(     "select *
			  		                    from tb_userspasswordsrecoveries a
						          inner join tb_users b using( iduser )
						          inner join tb_persons c using( idperson )
						               where a.idrecovery = :idrecovery
						                 and a.dtrecovery is NULL
						                 and date_add( a.dtregister, interval 1 hour ) > now();",
						          array( ":idrecovery" => $idrecovery )
						       );

		if ( count( $results ) === 0 )
		{
			throw new \Exception( "Não foi possível recuperar a senha!" );
		}
		else
		{
			return $results[ 0 ];
		}

	}

	public static function setForgotUsed( $idrecovery )
	{

		$sql = new Sql();
		$sql->query( "update tb_userspasswordsrecoveries set dtrecovery = now() where idrecovery = :idrecovery",
					 array( ":idrecovery" => $idrecovery ) );

	}

	public function setPassword( $password )
	{

		$sql = new Sql();
		$sql->query( "update tb_users set despassword = :password where iduser = :iduser", 
		  			 array( ":password" => password_hash( $password, PASSWORD_DEFAULT, [ "cost" => 12 ] ),
		  			 		":iduser"   => $this->getiduser()
		  		   ));
	}

}

 ?>