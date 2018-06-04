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
	load_theme_textdomain( 'storefront-inkston', get_template_directory() . '/languages' );

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

	/**
	 * This theme uses wp_nav_menu() in one location.
	 */
	register_nav_menus( array(
		//'top'		 => __( 'Top Menu', 'storefront-inkston' ),
		//'hamburger'	 => __( 'Hamburger Menu', 'storefront-inkston' ),
		//'primary'	 => __( 'Primary Menu', 'storefront-inkston' ),
		'social' => __( 'Social Menu', 'storefront-inkston' ),
		'footer' => __( 'Footer Menu', 'storefront-inkston' ),
	) );
	/* this code handles non-Polylang subsite allowing different language menus */
	if ( ! function_exists( 'pll_the_languages' ) ) {
		register_nav_menus( array(
			//'topfr_FR'		 => __( 'Top Menu', 'storefront-inkston' ) . ' Français',
			//'topes_ES'		 => __( 'Top Menu', 'storefront-inkston' ) . ' Español',
			//'topde_DE'		 => __( 'Top Menu', 'storefront-inkston' ) . ' Deutsche',
			'footerfr_FR'	 => __( 'Footer Menu', 'storefront-inkston' ) . ' Français',
			'footeres_ES'	 => __( 'Footer Menu', 'storefront-inkston' ) . ' Español',
			'footerde_DE'	 => __( 'Footer Menu', 'storefront-inkston' ) . ' Deutsche',
		) );
	}
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
	<header class="entry-header">
		<?php
		//storefront_post_thumbnail( 'full' );
		the_title( '<h1 class="entry-title">', '</h1>' );
		?>
	</header><!-- .entry-header -->
	<?php
}

include_once( 'inkston-customizer.php' );
