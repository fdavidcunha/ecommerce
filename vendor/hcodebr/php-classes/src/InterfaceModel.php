<?php

	namespace Hcode;

	interface InterfaceModel
	{
	    public function setData( $data );
	    public function getValues();
	    public function __call($name, $args);
	}

?>