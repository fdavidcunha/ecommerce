<?php

	use \Hcode\Page;

	/* Rota para a index do site */
	$app->get('/', function() {
    
		$page = new Page();
		$page->setTpl( "index" );

	});

?>