<?php

	use \Hcode\Page;
	use \Hcode\Model\Product;

	/* Rota para a index do site */
	$app->get('/', function() {
    
		$products = Product::listAll();

		$page = new Page();
		$page->setTpl( "index", [ 'products' => Product::checkList( $products ) ] );

	});

?>