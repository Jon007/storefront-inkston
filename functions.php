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


	set_post_thumbnail_size( 300, 300, true );

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
 * standard storefront brighten is absurd, just fades out text that needs to stand out,
 * so adjust it here?
 */
function inkston_brighten_factor() {
	return -25;
}

function inkston_darken_factor() {
	return -25;
}

//$brighten_factor       = apply_filters( 'storefront_brighten_factor', 25 );
//$darken_factor         = apply_filters( 'storefront_darken_factor', -25 );
//add_filter( 'storefront_brighten_factor', 'inkston_brighten_factor' );
//add_filter( 'storefront_darken_factor', 'inkston_darken_factor' );




/* override storefront disaster hover styles */
function inkston_customizer_css( $styles ) {
	$storefront_customizer	 = new Storefront_Customizer();
	$storefront_theme_mods	 = $storefront_customizer->get_storefront_theme_mods();
	$override_styles		 = '
	.main-navigation ul li a:hover,
	.main-navigation ul li:hover > a,
	.site-title a:hover,
	a.cart-contents:hover,
	.site-header-cart:hover > li > a,
	.site-header ul.menu li.current-menu-item > a {
	color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'header_link_color' ], inkston_darken_factor() ) . ';
	background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'background_color' ], inkston_darken_factor() ) . ';}
	a:hover{color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'header_link_color' ], inkston_darken_factor() ) . ';
}
	.menu-item a:hover, .wishlist_products_counter a:hover{color:' . $storefront_theme_mods[ 'accent_color' ] . ';}
	.tooltip .tooltiptext {background-color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'background_color' ], inkston_darken_factor() ) . ';}
	.widget_shopping_cart{opacity: 0.7;background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'background_color' ], inkston_darken_factor() ) . ';}
.site-header-cart .widget_shopping_cart, .site-header-cart .widget_shopping_cart a {color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'text_color' ], inkston_darken_factor() ) . '!important;}
.site-header-cart .widget_shopping_cart a:hover {color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'text_color' ], inkston_darken_factor() * 2 ) . '!important;}
	';

	return str_replace( '  ', ' ', $styles . $override_styles );
}

add_filter( 'storefront_customizer_css', 'inkston_customizer_css', 10, 1 );


/*
 * add inkston extra customizer settings
 * TODO: THIS DOES NOT WORK - WHY??
 */
function ink_customize_register( $wp_customize ) {
	$wp_customize->add_setting( 'storefront_action_color', array(
		'default'			 => apply_filters( 'storefront_action_color', '#43454b' ),
		'sanitize_callback'	 => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'storefront_action_color', array(
		'label'				 => __( 'Call to action color', 'storefront' ),
		'section'			 => 'storefront_typography',
		'settings'			 => 'storefront_action_color',
		'priority'			 => 60,
		'sanitize_callback'	 => 'sanitize_hex_color',
	) ) );
}

add_action( 'customize_register', 'ink_customize_register', 90, 1 );
/**
 * add inkston to the Storefront theme mods.
 *
 * @param array $storefront_theme_mods The Storefront Theme Mods.
 * @return array $storefront_theme_mods The Storefront Theme Mods.
 */
function inkston_theme_mods( $storefront_theme_mods ) {
	$storefront_theme_mods[ 'storefront_action_color' ] = get_theme_mod( 'storefront_action_color' );
	return $storefront_theme_mods;
}

add_filter( 'storefront_theme_mods', 'inkston_theme_mods', 10, 1 );
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
