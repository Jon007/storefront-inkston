<?php

/*
 * Inkston customizer:
 * - optionally, adjust brighten / darken factor (shouldnt this depend on whether using dark or light theme??)
 * - add storefront_action_color (.saleflash and .saleflash.inverse)
 * - additional hover and header customizations
 */


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




/* override storefront disaster hover styles and add inkston customizations for:
  title header semi-transparency and saleflash colours */
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
	a:hover{color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'accent_color' ], inkston_darken_factor() ) . ';}
	.menu-item a:hover, .wishlist_products_counter a:hover{color:' . $storefront_theme_mods[ 'accent_color' ] . ';}
	.tooltip .tooltiptext {background-color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'background_color' ], inkston_darken_factor() ) . ';}
	.widget_shopping_cart{opacity: 0.7;background-color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'background_color' ], inkston_darken_factor() ) . ';}
.site-header-cart .widget_shopping_cart, .site-header-cart .widget_shopping_cart a {color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'text_color' ], inkston_darken_factor() ) . '!important;}
.site-header-cart .widget_shopping_cart a:hover {color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'text_color' ], inkston_darken_factor() * 2 ) . '!important;}
div[data-featured-image] .col-full{background-color:' . $storefront_theme_mods[ 'background_color' ] . ';}
.saleflash{color:' . $storefront_theme_mods[ 'storefront_action_color' ] . '!important;background-color:' . $storefront_theme_mods[ 'background_color' ] . '!important;font-weight:bold;text-align: center;}
.saleflash.inverse{background-color:' . $storefront_theme_mods[ 'storefront_action_color' ] . '!important;color:' . $storefront_theme_mods[ 'background_color' ] . '!important;}
a.saleflash:hover, a.saleflash:active{color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'storefront_action_color' ], inkston_darken_factor() ) . '!important;}
a.saleflash.inverse:hover, a.saleflash.inverse:active{background-color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'storefront_action_color' ], inkston_darken_factor() ) . '!important;color:' . $storefront_theme_mods[ 'background_color' ] . '!important;}
#content .button{background-color:' . $storefront_theme_mods[ 'button_alt_background_color' ] . ';color:' . $storefront_theme_mods[ 'button_alt_text_color' ] . ';}
#content :hover.button,
#content :active.button,
#content :focus.button{background-color:' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'button_alt_background_color' ], inkston_darken_factor() ) . ';}
.handheld-navigation .dropdown-toggle { color: ' . $storefront_theme_mods[ 'text_color' ] . ';border-color: ' . $storefront_theme_mods[ 'text_color' ] . ';}
	';

	/* allow secondary menu within footer to follow customizer footer colour scheme */
	$override_styles .= '.site-footer .secondary-navigation ul ul,
.site-footer .secondary-navigation ul.menu ul{
background-color:' . $storefront_theme_mods[ 'footer_background_color' ] . '
}
.site-footer .secondary-navigation a {
color:' . $storefront_theme_mods[ 'footer_link_color' ] . '
}
@media screen and ( min-width: 768px ) {
.site-footer .secondary-navigation ul.menu a{
color: ' . $storefront_theme_mods[ 'footer_link_color' ] . ';
}
.site-footer .secondary-navigation ul.menu a:hover {
color: ' . storefront_adjust_color_brightness( $storefront_theme_mods[ 'footer_link_color' ], inkston_darken_factor() ) . ';
}}';
	return str_replace( '	', '', str_replace( '  ', ' ', $styles . $override_styles ) );
}

add_filter( 'storefront_customizer_css', 'inkston_customizer_css', 10, 1 );


/*
 * add inkston extra customizer settings
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


	/* -----------------------------------------------------------
	 * Copyright section
	 * ----------------------------------------------------------- */
	$wp_customize->add_section(
	'inkston_custom_copyright', array(
		'title'		 => __( 'Footer Copyright', 'storefront-inkston' ),
		'priority'	 => 600
	)
	);
	$wp_customize->add_setting(
	'copyright_txt', array(
		'default'			 => 'All rights reserved',
		'sanitize_callback'	 => 'sanitize_text_field',
		'transport'			 => 'postMessage'
	)
	);
	// Copyright CONTROL
	$wp_customize->add_control(
	'copyright_txt', array(
		'section'	 => 'inkston_custom_copyright',
		'label'		 => __( 'Copyright', 'storefront-inkston' ),
		'type'		 => 'text'
	)
	);
}

add_action( 'customize_register', 'ink_customize_register', 90, 1 );
/*
 * default values for additional customizer settings
 *
 * @param array $args	built-in storefront defaults.
 * @return array
 */
function ink_setting_default_values( $args ) {
	$args[ 'storefront_action_color' ] = '#fa7c0b';
	return $args;
}

add_filter( 'storefront_setting_default_values', 'ink_setting_default_values', 10, 1 );
/**
 * add inkston to the Storefront theme mods.
 *
 * @param array $storefront_theme_mods The Storefront Theme Mods.
 * @return array
 */
function inkston_theme_mods( $storefront_theme_mods ) {
	$storefront_theme_mods[ 'storefront_action_color' ] = get_theme_mod( 'storefront_action_color' );
	return $storefront_theme_mods;
}

add_filter( 'storefront_theme_mods', 'inkston_theme_mods', 10, 1 );
