<?php

	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Product;

	# Rota para a lista de produtos.
	$app->get( "/admin/products", function(){

		User::verifyLogin();

		$products = Product::listAll();

		$page = new PageAdmin();
		$page->setTpl( "products", [ "products" => $products ] );

	});

	# Rota para a página de criação do produto.
	$app->get( "/admin/products/create", function(){

		User::verifyLogin();

		$page = new PageAdmin();
		$page->setTpl( "products-create" );

	});

	# Rota para salvar o produto.
	$app->post( "/admin/products/create", function(){

		User::verifyLogin();

		$product = new Product();
		$product->setData( $_POST );
		$product->save();

		header( "location: /admin/products" );
		exit();

	});

	# Rota para a página de edição do produto.
	$app->get( "/admin/products/:idproduct", function( $idproduct ){

		User::verifyLogin();

		$product = new Product();
		$product->get( (int)$idproduct );

		$page = new PageAdmin();
		$page->setTpl( "products-update", [ 'product' => $product->getValues() ] );

	});

	# Rota para salvar a edição do produto.
	$app->post( "/admin/products/:idproduct", function( $idproduct ){

		User::verifyLogin();

		$product = new Product();
		$product->get( (int)$idproduct );
		$product->setData( $_POST );
		$product->save();
		
		if ( (int)$_FILES[ "file" ][ "size" ] > 0 ) {

			$product->setPhoto( $_FILES[ "file" ] );
		}

		header( "location: /admin/products" );
		exit();

	});

	# Rota para excluir o produto.
	$app->get( "/admin/products/:idproduct/delete", function( $idproduct ){

		User::verifyLogin();

		$product = new Product();
		$product->get( (int)$idproduct );
		$product->delete();

		header( "location: /admin/products" );
		exit();

	});

?>