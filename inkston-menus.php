<?php
/*
 * allow different menus for different locales
 */
function inkston_setup_menus() {
	/**
	 * setup extra menus
	 */
	register_nav_menus( array(
		'social' => __( 'Social Menu', 'storefront-inkston' ),
		'footer' => __( 'Footer Menu', 'storefront-inkston' ),
	) );
	/* this code handles non-Polylang subsite using JSM_User_Locale to have different language menus */
	if ( ! function_exists( 'pll_the_languages' ) && class_exists( 'JSM_User_Locale' ) ) {
		register_nav_menus( array(
			'primaryfr_FR'	 => __( 'Primary Menu', 'storefront-inkston' ) . ' Français',
			'primaryes_ES'	 => __( 'Primary Menu', 'storefront-inkston' ) . ' Español',
			'primaryde_DE'	 => __( 'Primary Menu', 'storefront-inkston' ) . ' Deutsche',
			'secondaryfr_FR' => __( 'Secondary Menu', 'storefront-inkston' ) . ' Français',
			'secondaryes_ES' => __( 'Secondary Menu', 'storefront-inkston' ) . ' Español',
			'secondaryde_DE' => __( 'Secondary Menu', 'storefront-inkston' ) . ' Deutsche',
			'handheldfr_FR'	 => __( 'Handheld Menu', 'storefront-inkston' ) . ' Français',
			'handheldyes_ES' => __( 'Handheld Menu', 'storefront-inkston' ) . ' Español',
			'handheldde_DE'	 => __( 'Handheld Menu', 'storefront-inkston' ) . ' Deutsche',
			'footerfr_FR'	 => __( 'Footer Menu', 'storefront-inkston' ) . ' Français',
			'footeres_ES'	 => __( 'Footer Menu', 'storefront-inkston' ) . ' Español',
			'footerde_DE'	 => __( 'Footer Menu', 'storefront-inkston' ) . ' Deutsche',
		) );
	}
}

add_action( 'after_setup_theme', 'inkston_setup_menus' );
function storefront_secondary_navigation() {
	$theme_location = 'secondary';
	if ( ! function_exists( 'pll_the_languages' ) && class_exists( 'JSM_User_Locale' ) ) {
		$locale = get_locale();
		switch ( $locale ) {
			case 'fr_FR':
				if ( has_nav_menu( 'secondaryfr_FR' ) ) {
					$theme_location = 'secondaryfr_FR';
				}
				break;
			case 'es_ES':
				if ( has_nav_menu( 'secondaryes_ES' ) ) {
					$theme_location = 'secondaryes_ES';
				}
				break;
			case 'de_DE':
				if ( has_nav_menu( 'secondaryde_DE' ) ) {
					$theme_location = 'secondaryde_DE';
				}
				break;
		}
	}

	if ( has_nav_menu( $theme_location ) ) {
		?>
		<nav class="secondary-navigation" role="navigation" aria-label="<?php esc_html_e( 'Secondary Navigation', 'storefront' ); ?>">
			<?php
			wp_nav_menu(
			array(
				'theme_location' => $theme_location,
				'fallback_cb'	 => '',
			)
			);
			?>
		</nav><!-- #site-navigation -->
		<?php
	}
}

/**
 * Display Primary Navigation
 *
 * @since  1.0.0
 * @return void
 */
function storefront_primary_navigation() {
	$primary_location	 = 'primary';
	$handheld_location	 = 'handheld';

	if ( ! function_exists( 'pll_the_languages' ) && class_exists( 'JSM_User_Locale' ) ) {
		$locale = get_locale();
		switch ( $locale ) {
			case 'fr_FR':
				if ( has_nav_menu( 'secondaryfr_FR' ) ) {
					$primary_location	 = 'primaryfr_FR';
					$handheld_location	 = 'handheldfr_FR';
				}
				break;
			case 'es_ES':
				if ( has_nav_menu( 'secondaryes_ES' ) ) {
					$primary_location	 = 'primaryes_ES';
					$handheld_location	 = 'handheldes_ES';
				}
				break;
			case 'de_DE':
				if ( has_nav_menu( 'secondaryde_DE' ) ) {
					$primary_location	 = 'primaryde_DE';
					$handheld_location	 = 'handheldde_DE';
				}
				break;
		}
	}
	?>
	<nav id="site-navigation" class="main-navigation" role="navigation" aria-label="<?php esc_html_e( 'Primary Navigation', 'storefront' ); ?>">
		<button class="menu-toggle" aria-controls="site-navigation" aria-expanded="false"><span><?php echo esc_attr( apply_filters( 'storefront_menu_toggle_text', __( 'Menu', 'storefront' ) ) ); ?></span></button>
				<?php
				if ( has_nav_menu( $primary_location ) ) {
					wp_nav_menu(
					array(
						'theme_location'	 => $primary_location,
						'container_class'	 => 'primary-navigation',
					)
					);
				}

				if ( has_nav_menu( $handheld_location ) ) {
					wp_nav_menu(
					array(
						'theme_location'	 => $handheld_location,
						'container_class'	 => 'handheld-navigation',
					)
					);
				}
				?>
	</nav><!-- #site-navigation -->
	<?php
}
