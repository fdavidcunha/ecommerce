<?php

	use \Hcode\Page;
	use \Hcode\Model\Product;
	use \Hcode\Model\Category;

	/* Rota para a index do site */
	$app->get('/', function() {
    
		$products = Product::listAll();

		$page = new Page();
		$page->setTpl( "index", [ 'products' => Product::checkList( $products ) ] );

	});

	// Rota para a clicar no item da lista de categorias, no rodapé do site.
	$app->get( "/category/:idcategory", function( $idcategory ) {

		$category = new Category();
		$category->get( (int)$idcategory );

		$page = new Page();
		$page->setTpl( "category", [ 
			'category' => $category->getValues(),
			'products' => Product::checkList( $category->getProducts() )
		]);
	});

?>