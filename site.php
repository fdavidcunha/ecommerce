<?php

	use \Hcode\Page;
	use \Hcode\Model\Product;
	use \Hcode\Model\Category;
	use \Hcode\Model\Cart;
	use \Hcode\Model\Address;
	use \Hcode\Model\User;

	/* Rota para a index do site */
	$app->get('/', function() {
    
		$products = Product::listAll();

		$page = new Page();
		$page->setTpl( "index", [ 'products' => Product::checkList( $products ) ] );

	});

	// Rota para a clicar no item da lista de categorias, no rodapé do site.
	$app->get( "/category/:idcategory", function( $idcategory ) {

		$page = ( isset( $_GET[ 'page' ] ) ) ? (int)$_GET[ 'page' ] : 1;

		$category = new Category();
		$category->get( (int)$idcategory );

		$pagination = $category->getProductsPage( $page );

		$pages = [];

		for ( $i = 1; $i <= $pagination[ 'pages' ] ; $i++ ) { 
			
			array_push( $pages, [ 'link' => '/category/' . $category->getidcategory() . '?page=' . $i,
			                      'page' => $i ] );
		}

		$page = new Page();
		$page->setTpl( "category", [ 
			'category' => $category->getValues(),
			'products' => $pagination[ 'data' ],
			'pages'    => $pages
		]);
	});

	$app->get( "/products/:desurl", function( $desurl ) {

		$product = new Product();
		$product->getFromURL( $desurl );

		$page = new Page();
		$page->setTpl( "product-detail", [ 
			'product'    => $product->getValues(),
			'categories' => $product->getCategories()
		]);
	});

	$app->get( "/cart", function(){

		// Obtendo o carrinho do usuário.
		$cart = Cart::getFromSession();

		$page = new Page();
		$page->setTpl( "cart", [ 
			'cart'     => $cart->getValues(),
			'products' => $cart->getProducts(),
			'error'    => Cart::getMsgError()
		] );

	} );

	$app->get( "/cart/:idproduct/add", function( $idproduct ){

		$product = new Product();
		$product->get( (int)$idproduct );

		$cart = Cart::getFromSession();

		$qtd = ( isset( $_GET[ 'qtd' ] ) ) ? (int)$_GET[ 'qtd' ] : 1;

		// Adicionando a quantidade de produtos informada no site.
		for ( $i = 0; $i < $qtd; $i++ ){
			
			$cart->addProduct( $product );
			
		}

		header( "location: /cart" );
		exit();

	} );

	$app->get( "/cart/:idproduct/minus", function( $idproduct ){

		$product = new Product();
		$product->get( (int)$idproduct );

		$cart = Cart::getFromSession();
		$cart->removeProduct( $product );

		header( "location: /cart" );
		exit();
		
	} );

	$app->get( "/cart/:idproduct/remove", function( $idproduct ){

		$product = new Product();
		$product->get( (int)$idproduct );

		$cart = Cart::getFromSession();
		$cart->removeProduct( $product, true );

		header( "location: /cart" );
		exit();
		
	} );

	$app->post( "/cart/freight", function() {

		$cart = Cart::getFromSession();
		$cart->setFreight( $_POST[ 'zipcode' ] );

		header( "location: /cart" );
		exit();

	} );

	$app->get( "/checkout", function(){

		User::verifyLogin( false );

		$cart = Cart::getFromSession();

		$address = new Address();

		$page = new Page();
		$page->setTpl( "checkout", [
				'cart'    => $cart->getValues(),
				'address' => $address->getValues()

		] );

	} );

	$app->get( "/login", function(){

		$page = new Page();
		$page->setTpl( "login", [
			'error'          => User::getError(),
			'errorRegister'  => User::getErrorRegister(),
			'registerValues' => ( isset( $_SESSION[ 'registerValues' ] ) ) ? $_SESSION[ 'registerValues' ] : [ 'name' => '', 'email' => '', 'phone' => '' ]
		]);

	} );

	$app->post( "/login", function(){

		try {

			User::login( $_POST[ 'login' ], $_POST[ 'password' ] );
			
		} catch ( Exception $e ) {
			
			User::setError( $e->getMessage() );

		}
		
		header( "location: /checkout" );
		exit();

	} );

	$app->get( "/logout", function(){

		User::logout();

		Cart::removeToSession();
   		
   		session_regenerate_id();		

		header( "location: /login" );
		exit();

	} );

	$app->post( "/register", function(){

		# Salvando as informações que o usuário já digitou.
		$_SESSION[ 'registerValues' ] = $_POST;

		if ( !isset( $_POST[ 'name' ] ) || $_POST[ 'name' ] == '' ) { 

			User::setErrorRegister( "Preencha o seu nome." );

			header( "location: /login" );
			exit();
		}

		if ( !isset( $_POST[ 'email' ] ) || $_POST[ 'email' ] == '' ) { 

			User::setErrorRegister( "Preencha o seu e-mail." );

			header( "location: /login" );
			exit();
		}

		if ( !isset( $_POST[ 'password' ] ) || $_POST[ 'password' ] == '' ) { 

			User::setErrorRegister( "Preencha a senha." );

			header( "location: /login" );
			exit();
		}

		if ( User::checkLoginExist( $_POST[ 'email' ] ) === true )
		{

			User::setErrorRegister( "Endereço de e-mail já utilizado por outro usuário." );

			header( "location: /login" );
			exit();
		}

		$user = new User;
		
		$user->setData( [
			'inadmin'     => 0,
			'deslogin'    => $_POST[ 'email' ],
			'desperson'   => $_POST[ 'name' ],
			'desemail'    => $_POST[ 'email' ],
			'despassword' => $_POST[ 'password' ],
			'nrphone'     => $_POST[ 'phone' ]
		]);

		$user->save();

		User::login( $_POST[ 'email' ], $_POST[ 'password' ] );

		header( "location: /checkout" );
		exit();

	} );

?>