<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit;
// BEGIN ENQUEUE PARENT ACTION
// AUTO GENERATED - Do not modify or remove comment markers above or below:
// END ENQUEUE PARENT ACTION


/*
 * inkston specific setup only: generic add_theme_support() mostly done by storefront
 */
function inkston_setup() {

	/* Make theme available for translation */
	load_theme_textdomain( 'storefront-inkston', get_stylesheet_directory() . '/languages' );

	/* Enable support for Excerpt on Pages. See http://codex.wordpress.org/Excerpt */
	add_post_type_support( 'page', 'excerpt' );

	//allow forums to have featured images
	add_post_type_support( 'forum', array( 'thumbnail' ) );
	add_post_type_support( 'topic', array( 'thumbnail' ) );

	//this is done for the tiles, but StoreFront hates it actually...
	//so we could get inkston to use a different size for the tiles instead
	//(actually it already does, it uses medium)
	//set_post_thumbnail_size( 300, 300, true );
	set_post_thumbnail_size( 1500, 240, false ); //or as appropriate for storefront
	//currently storefront only does this if woocommerce is activated, we want search in all cases
	add_action( 'storefront_header', 'storefront_product_search', 40 );

	//move post meta to after post, it's not important in this theme
	remove_action( 'storefront_loop_post', 'storefront_post_meta', 20 );
	remove_action( 'storefront_single_post', 'storefront_post_meta', 20 );
	add_action( 'storefront_single_post', 'storefront_post_meta', 40 );
}

add_action( 'after_setup_theme', 'inkston_setup' );

/*
 * adds:
 *  - inkston main.js with a few js tricks including smooth scroll to top
 *  - inkston.css with auto-version update (standard style.css inclusion doesn't do this)
 */
function inkston_scripts() {
	$suffix			 = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$template_uri	 = get_stylesheet_directory_uri();
	$scriptname		 = '/inkston' . $suffix . '.css';
	wp_enqueue_style( 'inkston-style', $template_uri . $scriptname, array(), filemtime( get_stylesheet_directory() . $scriptname ) );


	$scriptname	 = '/js/main' . $suffix . '.js';
	wp_enqueue_script( 'inkston-main', $template_uri . $scriptname, array( 'jquery' ), filemtime( get_stylesheet_directory() . $scriptname ), true );
	?><script type="text/javascript">window.loginurl = '<?php
	$referer	 = (isset( $_SERVER[ 'REQUEST_URI' ] ) ? $_SERVER[ 'REQUEST_URI' ] : '');
	echo(wp_login_url( $referer ))
	?>';</script><?php
}

add_action( 'wp_enqueue_scripts', 'inkston_scripts', 1000 );


/*
 * use inkston_cart_link if it exists, if not fallback to custom version of storefront
 */
function storefront_cart_link() {
	if ( function_exists( 'inkston_cart_link' ) ) {
		inkston_cart_link();
	} else {
		echo('<a class="cart-contents" href="' . esc_url( wc_get_cart_url() ) . '" title="' .
		esc_attr_e( 'View your shopping cart', 'storefront' ) .
		'<span class="count">' . WC()->cart->get_cart_contents_count() . '</span>' .
		wp_kses_post( WC()->cart->get_cart_subtotal() ) . '</a>');
	}
}

/* storefront hero header images not compatible with inkston square featured images */
function remove_header_background_image( $styles ) {
	if ( isset( $styles[ 'background-image' ] ) ) {
		unset( $styles[ 'background-image' ] );
		$featured_image = get_the_post_thumbnail_url( get_the_ID(), 'full' );
		if ( $featured_image ) {
			$background_image				 = 'url(' . esc_url( $featured_image ) . ')';
			$styles[ 'background-image' ]	 = $background_image;
			//error_log( $styles[ 'background-image' ] );
		}
	}
	return $styles;
}

add_filter( 'storefront_homepage_content_styles', 'remove_header_background_image', 10, 1 );
/* storefront large header images not compatible with inkston square featured images */
function storefront_page_header() {
	?>
	<header class="entry-header"><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<?php
		//storefront_post_thumbnail( 'full' );
		the_title( '<h1 class="entry-title">', '</h1>' );
		?>
	</header><!-- .entry-header -->
	<?php
}

/**
 * Filters the post thumbnail HTML to wrap in <a> tag to work with photoswipe
 *
 * @since 2.9.0
 *
 * @param string       $html              The post thumbnail HTML.
 * @param int          $post_id           The post ID.
 * @param string       $post_thumbnail_id The post thumbnail ID.
 * @param string|array $size              The post thumbnail size. Image size or array of width and height
 *                                        values (in that order). Default 'post-thumbnail'.
 * @param string       $attr              Query string of attributes.
 */
function thumbnail_lightbox( $html, $postID, $post_thumbnail_id, $size, $attr ) {
	if ( $post_thumbnail_id ) {
		$image = wp_get_attachment_image_src( $post_thumbnail_id, 'full', false );
		if ( $image ) {
			list($src, $width, $height) = $image;
			$html = '<a class="post-thumbnail" href="' . $src . '" data-size="' . $width . 'x' . $height . '">' . $html . '</a>';
		}
	}
	return $html;
}

//sadly general filter of thumbnail_lightbox didn't work as filtered too many images...
//.. so instead rewrite the storefront function and do it specifically there
function storefront_post_thumbnail( $size = 'full' ) {
	if ( has_post_thumbnail() ) {
add_filter( 'post_thumbnail_html', 'thumbnail_lightbox', 10, 5 );
		the_post_thumbnail( $size );
		remove_filter( 'post_thumbnail_html', 'thumbnail_lightbox', 10 );
	}
}

/**
 * Display Product Search
 *
 * @since  1.0.0
 * @uses  storefront_is_woocommerce_activated() check if WooCommerce is activated
 * @return void
 */
function storefront_product_search() {
	?><div class="site-search">
	<?php
		//the_widget( 'WC_Widget_Product_Search', 'title=' );
		get_search_form();
		?>
	</div><?php
}

/*
 * add a recent posts section to the homepage, before the woocommerce sections
 */
function inkston_recent_posts() {
	echo '<section class="storefront-product-section inkston-recent-stories" aria-label="' . esc_attr__( 'Recent Stories', 'storefront-inkston' ) . '">';

	echo '<h2 class="section-title">' . __( 'Recent Stories', 'storefront-inkston' ) . '</h2>';


	output_recent_post_tiles();
	$stories_category_id = (function_exists( 'pll_get_term' )) ? pll_get_term( 212 ) : 212;
	echo '<p style="clear:both"><a href="' . get_category_link( $stories_category_id ) . '">';
	_e( 'Read more', 'storefront-inkston' );
	echo '</a></p>';
	echo '</section>';
}

add_action( 'homepage', 'inkston_recent_posts', 15 );
function output_recent_post_tiles() {
	$query_args	 = array(
		'ignore_sticky_posts'	 => true, //sticky posts automatically added by WP
		'post_type'				 => array( 'post' ),
		'orderby'				 => 'modified',
		'posts_per_page'		 => 4,
		'showposts'				 => 4,
		'order'					 => 'DESC',
		'fields'				 => 'ids',
		'tax_query'				 => [
			[
				'taxonomy'	 => 'category',
				'field'		 => 'slug',
				'terms'		 => 'stories'
			],
		]
	);
	$recent_list = get_posts( $query_args );
	foreach ( $recent_list as $post_id ) {
		global $post;
		$post = get_post( $post_id );
		setup_postdata( $post );
		tile_thumb();
	}
}

include_once( 'inkston-customizer.php' );
include_once( 'inkston-menus.php' );


/*
 * dequeue all unnecessary scripts
 */
function inkston_dequeue_script() {
    if ( class_exists( 'woocommerce' ) ) {
        /* avoid refreshing cart fragments on pages which are not cached anyway...
          especially cart/checkout pages are already over-heavy due to not being cached and extra stripe scripts etc */
        if ( is_cart() || is_checkout() || isset( $_GET[ 'pay_for_order' ] ) || is_add_payment_method_page() || is_account_page() ) {
            wp_dequeue_script( 'wc-cart-fragments' );
        }
        //could restrict social login to My Account and Checkout but stripping out single pages removes payload from most urls
        if ( ! is_page() || ( is_shop()) ) {
            wp_dequeue_script( 'the_champ_sl_common' ); //super-socializer
            wp_dequeue_script( 'the_champ_ss_general_scripts' );//super-socializer
            wp_dequeue_script( 'front-js' );  //advanced shipment tracking                                       
        }
        if (! is_product() ){
            wp_dequeue_script( 'woosb-frontend' );  //product bundles 
            wp_dequeue_script( 'tinvwl' );  //wishlist
        }
        if ( ! is_single() || is_product() ){
            wp_dequeue_script( 'spu-public' );  //popups - only allow on article pages     
        }
    }
//    wp_dequeue_script( 'wpla_product_matcher');
}

add_action( 'wp_print_scripts', 'inkston_dequeue_script', 1000 );

/*
 * dequeue all unnecessary csss
 */
function inkston_dequeue_styles() {
        //$wp_styles = wp_styles();
	wp_dequeue_style( 'photoswipe-default-skin' );
	wp_dequeue_style( 'photoswipe' );        
	//wp_dequeue_style( 'pswp-skin' );
	//wp_dequeue_style( 'photoswipe-core-css' );
        
	wp_dequeue_style( 'storefront-child-style' ); //default style.css which only has text theme info
        
	wp_dequeue_style( 'decent-comments-widget' );
	//wp_dequeue_style( 'dashicons' );  //hamburger menus
	wp_dequeue_style( 'front_style' );  //woo advanced shipment tracking
	//wp_dequeue_style( 'tinvwl' );  //wishlist
	//wp_dequeue_style( 'storefront-fonts' );  //google font used by storefront

	wp_dequeue_style( 'badgeos-front' ); //badgeos pages
        wp_deregister_style( 'badgeos-front' );

	if ( class_exists( 'woocommerce' ) ) {
            if ( ! is_product() ) {
                wp_dequeue_style( 'woosb' );
                wp_dequeue_style( 'woosb-frontend' );  //smart bundle                                            
                wp_dequeue_style( 'tinvwl' );  //wishlist, add to wishlist functions
            }
            //could restrict social login to My Account and Checkout but stripping out single pages removes payload from most urls
            if ( ! is_page() || ( is_shop()) ) {
                wp_dequeue_style( 'the_champ_frontend_css' );
            }
        }        
        if ( class_exists( 'Classic_Editor' ) ) {
            //if ( is_multisite() ) {
                //$opt = get_network_option( null, 'classic-editor-allow-sites' );
                //if ( $opt !== 'allow' ) {
                    inkston_dequeue_woo_block_editors();
                //}
            //}
        }
}
function inkston_dequeue_woo_block_editors(){
    //$wp_styles = wp_styles();

    wp_dequeue_style('wp-block-library' );
    wp_dequeue_style('wp-block-library-theme' );
    wp_dequeue_style('wc-block-style' );
    wp_dequeue_style('wp-components' );
    wp_dequeue_style('wp-editor-font' );
    wp_dequeue_style('wp-block-editor' );
    wp_dequeue_style('wp-nux' );
    wp_dequeue_style('wp-editor-css' );
    wp_dequeue_style('block-robo-gallery-style-css' );
    wp_dequeue_style('storefront-gutenberg-blocks' );    
    wp_dequeue_style('storefront-gutenberg-blocks-inline' );    
}

add_action( 'wp_print_styles', 'inkston_dequeue_styles', 1000 );
