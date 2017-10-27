<?php

	use \Hcode\Page;
	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;

	// Rota para a lista de categorias.
	$app->get( "/admin/categories", function(){

		User::verifyLogin();

		$categories = Category::listAll();

		$page = new PageAdmin();
		$page->setTpl( "categories", [ 'categories' => $categories ] );
	});

	// Rota para a página de inclusão da categoria.
	$app->get( "/admin/categories/create", function(){

		User::verifyLogin();

		$page = new PageAdmin();
		$page->setTpl( "categories-create" );
	});

	// Rota para salvar a inclusão da categoria.
	$app->post( "/admin/categories/create", function(){

		User::verifyLogin();

		$category = new Category();
		$category->setData( $_POST );
		$category->save();

		header("Location: /admin/categories");
		exit();
	});

	// Rota para exclusão da categoria.
	$app->get( "/admin/categories/:idcategory/delete", function( $idcategory ){

		User::verifyLogin();

		$category = new Category();
		$category->get( (int)$idcategory );
		$category->delete();

		header("Location: /admin/categories");
		exit();
	});

	// Rota para a página de alteração da categoria.
	$app->get( "/admin/categories/:idcategory", function( $idcategory ){

		User::verifyLogin();

		$category = new Category();
		$category->get( (int)$idcategory );
		
		$page = new PageAdmin();
		$page->setTpl( "categories-update", [ 'category' => $category->getValues() ] );

	});

	// Rota para salvar a alteração da categoria.
	$app->post( "/admin/categories/:idcategory", function( $idcategory ){

		User::verifyLogin();

		$category = new Category();
		$category->get( (int)$idcategory );
		$category->setData( $_POST );
		$category->save();
		

		header("Location: /admin/categories");
		exit();
	});

	// Rota para acessar a lista de produtos por categorias ou sem categorias.
	$app->get( "/admin/categories/:idcategory/products", function( $idcategory ) {

		User::verifyLogin();

		$category = new Category();
		$category->get( (int)$idcategory );

		$page = new PageAdmin();
		$page->setTpl( "categories-products", [ 
			'category'           => $category->getValues(),
			'productsRelated'    => [],
			'productsNotRelared' => []
		]);
	});

?>