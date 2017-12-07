<?php 

namespace Hcode\Model;

use \Hcode\DB\Sql;
use \Hcode\Model;
use \Hcode\Mailer;

class User extends Model {

	const SESSION        = "User";
	const SECRET         = "HcodePhp7_Secret";
	const ERROR          = "UserError";
	const ERROR_REGISTER = "UserErrorRegister";
	const SUCCESS        = "UserSuccess";

	public static function getFromSession()
	{

		$user = new User();

		if ( isset( $_SESSION[ User::SESSION ] ) && (int)$_SESSION[ User::SESSION ][ 'iduser' ] > 0 ){

			$user->setData( $_SESSION[ User::SESSION ] );

		}

		return $user;

	}

	public static function checkLogin( $inadmin = true )
	{

		# Sessão do usuário não está definida.
		# Sessão do usuário está definida mas está vazia.
		# Sessão do usuário está definida mas o id não é maior que zero.

		if ( !isset( $_SESSION[ User::SESSION ] ) || 
			 !$_SESSION[ User::SESSION ] ||
			 !(int)$_SESSION[ User::SESSION ][ "iduser" ] > 0 ) {

			# Não está logado.
			return false;

		} else {

			# É um usuário da administração e o usuário da sessão é um administrador.
			if ( $inadmin === true && (bool)$_SESSION[ User::SESSION ][ 'inadmin' ] === true ) {

				return true;

			# Não precisa de login.
			} else if ( $inadmin === false ) {

				return true;

			# Usuário não autenticado tentando acessar alguma página privada.
			} else {

				return false;

			}

		}

	}

	public static function login( $login, $password )
	{

		$db = new Sql();

		$consulta =    "select * 
  					      from tb_users u
				     left join tb_persons p 
				            on p.idperson = u.idperson
                         where u.deslogin = :login";

		$results = $db->select( $consulta, [ ":login" => $login ] );

		if ( count( $results ) === 0) {
			throw new \Exception( "Não foi possível fazer login." );
		}

		$data = $results[ 0 ];

		if ( password_verify( $password, $data[ "despassword" ] ) ) {

			$user = new User();
			
			$data[ 'desperson' ] = utf8_encode( $data[ 'desperson' ] );

			// Criando os métodos gets e sets do objeto, dinamicamente.
			$user->setData( $data );

			$_SESSION[ User::SESSION ] = $user->getValues();

			return $user;

		} else {

			throw new \Exception("Usuário inexistente ou senha inválida.");
		}
	}

	public static function logout()
	{
		// Excluindo a sessão.
		$_SESSION[User::SESSION] = NULL;
	}

	public static function verifyLogin( $inadmin = true )
	{
		
		if ( !User::checkLogin( $inadmin ) ) {

			if ( $inadmin ) {

				header( "location: /admin/login" );
				exit;

			} else {

				header( "location: /login" );
				exit;

			}

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

		
        if ( $this->getnrphone() == '' ) {

        	$this->setnrphone( null );

        }

		$results = $sql->select( "CALL sp_users_save(:desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin)", 
                                  array( 
                                  	":desperson"   => utf8_decode( $this->getdesperson() ),
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

		$data = $results[ 0 ];

		$data[ 'desperson' ] = utf8_encode( $data[ 'desperson' ] );

		$this->setData( $data );

	}

	public function update( $changePassword = true )
	{

		if ( $changePassword ) {

			$password = password_hash( $this->getdespassword(), PASSWORD_DEFAULT, [ "cost" => 12 ] );
			
		} else {

			$password = $_POST[ 'despassword' ];

		}

		$sql = new Sql();
		$results = $sql->select( "CALL sp_usersupdate_save( :iduser, :desperson, :deslogin, :despassword, :desemail, :nrphone, :inadmin )", 
                                  array( 
                                  	":iduser"      => $this->getiduser(),
                                  	":desperson"   => utf8_decode( $this->getdesperson() ),
	                                ":deslogin"    => $this->getdeslogin(),
	                                ":despassword" => $password,
	                                ":desemail"    => $this->getdesemail(),
	                                ":nrphone"     => $this->getnrphone(),
	                                ":inadmin"     => $this->getinadmin() ) 
	                           );

		$this->setData( $results[ 0 ] );
	}


	public function delete()
	{

		$sql = new Sql();
		$sql->query( "CALL sp_users_delete( :iduser )", array( ':iduser' => $this->getiduser() ) );

	}

	public static function getForgot( $email, $inadmin = true ) 
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

				if ( $inadmin === true ) {

					$link = "https://temsaboresaude.com.br/admin/forgot/reset?code=$code";

				} else {

					$link = "https://temsaboresaude.com.br/forgot/reset?code=$code";

				}

				$mailer = new Mailer( $data[ "desemail" ], $data[ "desperson" ], utf8_decode( "Redefinição de senha" ), "forgot", 
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

	public static function setError( $msg )
	{

		$_SESSION[ User::ERROR ] = $msg;

	}

	public static function getError()
	{

		# Se o erro estiver definido na session do usuário e não for vazio.
		$msg = ( isset( $_SESSION[ User::ERROR ] ) && $_SESSION[ User::ERROR ] ) ? $_SESSION[ User::ERROR ] : "";

		User::clearError();

		return $msg;

	}

	public static function clearError()
	{

		$_SESSION[ User::ERROR ] = NULL;
	}

	public static function setSuccess( $msg )
	{

		$_SESSION[ User::SUCCESS ] = $msg;

	}

	public static function getSuccess()
	{

		# Se o erro estiver definido na session do usuário e não for vazio.
		$msg = ( isset( $_SESSION[ User::SUCCESS ] ) && $_SESSION[ User::SUCCESS ] ) ? $_SESSION[ User::SUCCESS ] : "";

		User::clearSuccess();

		return $msg;

	}

	public static function clearSuccess()
	{

		$_SESSION[ User::SUCCESS ] = NULL;
	}

	public static function setErrorRegister( $msg )
	{

		$_SESSION[ User::ERROR_REGISTER ] = $msg;

	}

	public static function getErrorRegister()
	{

		$msg = ( isset( $_SESSION[ User::ERROR_REGISTER ] ) && $_SESSION[ User::ERROR_REGISTER ] ) ? $_SESSION[ User::ERROR_REGISTER ] : "";

		User::clearErrorRegister();

		return $msg;
	}

	public static function clearErrorRegister()
	{

		$_SESSION[ User::ERROR_REGISTER ] = NULL;
	}

	public static function checkLoginExist( $login )
	{

		$sql = new Sql();

		$results = $sql->select( "select * from tb_users where deslogin = :deslogin", [ ':deslogin' => $login ] );

		return ( count( $results ) > 0 );

	}

	public function getPasswordHash( $password )
	{

		return password_hash( $password, PASSWORD_DEFAULT, [ 'cost' => 12 ] );

	}

	public function getOrders()
	{

		$sql = new Sql();
		
		$results = $sql->select(     "select * 
			                            from tb_orders       a
			                      inner join tb_ordersstatus b using( idstatus )
			                      inner join tb_carts        c using( idcart )
			                      inner join tb_users        d on d.iduser = a.iduser
			                      inner join tb_addresses    e using ( idaddress )
			                      inner join tb_persons      f on f.idperson = d.idperson
			                           where a.iduser = :iduser", [ 
			                     ':iduser' => $this->getiduser()
		] );

		return $results;

	}

	// Query para paginação.
	public static function getPage( $page = 1, $itensPerPage = 10 )
	{

		$start = ( $page - 1 ) * $itensPerPage;

		$sql = new Sql();

		$results = $sql->select(     "select SQL_CALC_FOUND_ROWS * 
			                            from tb_users a
			                      inner join tb_persons b
			                           using ( idperson )
	                           		order by b.desperson
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
			                            from tb_users a
			                      inner join tb_persons b
			                           where b.desperson like :search
			                              or b.desemail like :search
			                              or a.deslogin like :search
			                           using ( idperson )
	                           		order by b.desperson
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