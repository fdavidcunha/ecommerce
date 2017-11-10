<?php

	use \Hcode\Page;
	use \Hcode\PageAdmin;
	use \Hcode\Model\User;
	use \Hcode\Model\Category;
	use \Hcode\Model\Product;

	// Rota para a lista de categorias.
	$app->get( "/admin/categories", function(){

		User::verifyLogin();

		$search = ( isset( $_GET[ 'search' ] ) ) ? $_GET[ 'search' ] : "";
		$page   = ( isset( $_GET[ 'page' ] ) ) ? $_GET[ 'page' ] : 1;

		if ( $search != '' ) {

			$pagination = Category::getPageSearch( $search, $page );

		} else {

			$pagination = Category::getPage( $page );

		}

		$pages = [];

		for ( $x = 0; $x < $pagination[ 'pages' ]; $x++ )
		{

			array_push( $pages, [
						'href' => '/admin/categories?' . http_build_query( [ 
							'page'   => $x + 1,
							'search' => $search
						] ),
						'text' => $x + 1
			 ] );

		}

		$page = new PageAdmin();
		$page->setTpl( "categories", [ 
			'categories' => $pagination[ 'data' ],
			'search'     => $search,
			'pages'      => $pages
		] );

	} );

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
			'productsRelated'    => $category->getProducts( true ),
			'productsNotRelated' => $category->getProducts( false )
		]);
	});

	// Rota para adicionar produtos a uma categoria.
	$app->get( "/admin/categories/:idcategory/products/:idproduct/add", function( $idcategory, $idproduct ) {

		User::verifyLogin();

		$product = new Product();
		$product->get( (int)$idproduct );

		$category = new Category();
		$category->get( (int)$idcategory );
		$category->addProduct( $product );

		header( "Location: /admin/categories/" . $idcategory . "/products" );
		exit();

	});

	// Rota para produto de uma categoria.
	$app->get( "/admin/categories/:idcategory/products/:idproduct/remove", function( $idcategory, $idproduct ) {

		User::verifyLogin();

		$product = new Product();
		$product->get( (int)$idproduct );

		$category = new Category();
		$category->get( (int)$idcategory );
		$category->removeProduct( $product );

		header( "Location: /admin/categories/" . $idcategory . "/products" );
		exit();

	});

?>