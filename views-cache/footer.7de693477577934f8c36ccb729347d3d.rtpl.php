<?php if(!class_exists('Rain\Tpl')){exit;}?>    <div class="footer-top-area">
        <div class="zigzag-bottom"></div>
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-6">
                    <div class="footer-about-us">
                        <h2>Sabor & Saúde</h2>
                        <p>Nascemos de um quiosque simples na rua e nos especializamos em alimentar bem as pessoas e de forma saudável.</p>
                        </br>
                        <p>Edf. Luciano Costa</p>
                        <p>Rua Dona Maria César, 170, Lj B</p>
                        <p>Recife Antigo - Recife/PE</p>
                        <p>CEP: 50.030-140</p>
                        <p>F: 81</p>
                        
                        <div class="footer-social">
                            <a href="https://www.facebook.com/saboresaudesaladadefrutas/" target="_blank">
                                <i class="fa fa-facebook"></i>
                            </a>
                            <a href="#" target="_blank">
                                <i class="fa fa-twitter"></i>
                            </a>
                            <a href="#" target="_blank">
                                <i class="fa fa-youtube"></i>
                            </a>
                            <a href="#" target="_blank">
                                <i class="fa fa-instagram"></i>
                            </a>
                            <a href="#" target="_blank">
                                <i class="fa fa-linkedin"></i>
                            </a>
                            <a href="#" target="_blank">
                                <i class="fa fa-google-plus"></i>
                            </a>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">Navegação </h2>
                        <ul>
                            <li><a href="#">Minha Conta</a></li>
                            <li><a href="#">Meus Pedidos</a></li>
                            <li><a href="#">Lista de Desejos</a></li>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-menu">
                        <h2 class="footer-wid-title">Categorias</h2>
                        <ul>
                            <?php require $this->checkTemplate("categories-menu");?>
                        </ul>                        
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6">
                    <div class="footer-newsletter">
                        <h2 class="footer-wid-title">Notícias e novidades</h2>
                        <p>Cadastre seu e-mail e fique por dentro de nossas novidades e promoções!</p>
                        <div class="newsletter-form">
                            <form action="#">
                                <input type="email" placeholder="Seu e-mail">
                                <input type="submit" value="Cadastrar">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End footer top area -->
    
    <div class="footer-bottom-area">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="copyright">
                        <p>&copy; 2017 Proprius Sistemas (proprius.sistemas@gmail.com). <a href="http://www.propriussistemas.com.br" target="_blank">www.propriussistemas.com.br</a></p>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="footer-card-icon">
                        <i class="fa fa-cc-sodexo"></i>
                        <i class="fa fa-cc-mastercard"></i>
                        <i class="fa fa-cc-paypal"></i>
                        <i class="fa fa-cc-visa"></i>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- End footer bottom area -->
   
    <!-- Javascript files -->
    <!-- Latest jQuery form server -->
    <script src="https://code.jquery.com/jquery.min.js"></script>
    <!-- Biblioteca JS que controla templates -->
    <script src="/res/site/js/handlebars-v4.0.11.js"></script>
    <!-- Bootstrap JS form CDN -->
    <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>
    <!-- jQuery sticky menu -->
    <script src="/res/site/js/owl.carousel.min.js"></script>
    <script src="/res/site/js/jquery.sticky.js"></script>
    <!-- jQuery easing -->
    <script src="/res/site/js/jquery.easing.1.3.min.js"></script>
    <!-- Main Script -->
    <script src="/res/site/js/main.js"></script>
    <!-- Slider -->
    <script type="text/javascript" src="/res/site/js/bxslider.min.js"></script>
	<script type="text/javascript" src="/res/site/js/script.slider.js"></script>
    <script type="text/javascript">
        $( function (){

            // Verificando se a variável scripts é uma instância de um array.
            if ( scripts instanceof Array ){

                // Percorrendo o array para executar as funções.
                $.each( scripts, function( index, fn ){

                    // Verificando se fn é uma função e executando a mesma.
                    if ( typeof fn === 'function' ) fn();
                });
            }

        });
    </script>
    <!-- SLIDER REVOLUTION 4.x SCRIPTS  -->
    <script type="text/javascript" src="/res/site/js/jquery.themepunch.tools.min.js"></script>
    <script type="text/javascript" src="/res/site/js/jquery.themepunch.revolution.min.js"></script>
    <!-- FLEX SLIDER SCRIPTS  -->
    <script defer src="/res/site/js/jquery.flexslider-min.js"></script>
    <!-- Pretty Photo JS -->
    <script src="/res/site/js/jquery.prettyPhoto.js"></script>
    <!-- Respond JS for IE8 -->
    <script src="/res/site/js/respond.min.js"></script>
    <!-- HTML5 Support for IE -->
    <script src="/res/site/js/html5shiv.js"></script>
    <!-- Custom JS -->
    <script src="/res/site/js/custom.js"></script>
    <!-- JS code for this page -->
    <script>
    /* ******************************************** */
    /*  JS for SLIDER REVOLUTION  */
    /* ******************************************** */
            jQuery(document).ready(function() {
                   jQuery('.tp-banner').revolution(
                    {
                        delay:9000,
                        startheight:500,
                        
                        hideThumbs:10,
                        
                        navigationType:"bullet",    
                                                    
                        hideArrowsOnMobile:"on",
                        
                        touchenabled:"on",
                        onHoverStop:"on",
                        
                        navOffsetHorizontal:0,
                        navOffsetVertical:20,
                        
                        stopAtSlide:-1,
                        stopAfterLoops:-1,

                        shadow:0,

                        fullWidth:"on",
                        fullScreen:"off"
                    });
            });
    /* ******************************************** */
    /*  JS for FlexSlider  */
    /* ******************************************** */
    
        $(window).load(function(){
            $('.flexslider-recent').flexslider({
                animation:      "fade",
                animationSpeed: 1000,
                controlNav:     true,
                directionNav:   false
            });
            $('.flexslider-testimonial').flexslider({
                animation:      "fade",
                slideshowSpeed: 5000,
                animationSpeed: 1000,
                controlNav:     true,
                directionNav:   false
            });
        });
    
    /* Gallery */

    jQuery(".gallery-img-link").prettyPhoto({
       overlay_gallery: false, social_tools: false
    });
    
    </script>


  </body>
</html>