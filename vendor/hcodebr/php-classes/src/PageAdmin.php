<?php 

namespace Hcode;

class PageAdmin extends Page {

	public function __construct( $opts = array(), $tpl_dir = "/views/admin/" )
	{
		$page = explode( "/", $_SERVER[ 'REQUEST_URI' ] );
		$opts[ 'page' ] = end( $page );
		parent::__construct( $opts, $tpl_dir );
	}
}

?>