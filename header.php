<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'understrap_container_type' );
?>
<!DOCTYPE html>
<html class="no-js" <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name="apple-mobile-web-app-title" content="<?php bloginfo( 'name' ); ?> - <?php bloginfo( 'description' ); ?>">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
	<!-- Google Tag Manager -->
	<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
	new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
	j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
	'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
	})(window,document,'script','dataLayer','GTM-KNDC5HH');</script>
	<!-- End Google Tag Manager -->
</head>

<body <?php body_class(); ?>>

<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KNDC5HH"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->

<div class="hfeed site" id="page">

	<div id="wrapper-navbar" class="fixed-top" itemscope itemtype="http://schema.org/WebSite">

		<a class="skip-link sr-only sr-only-focusable" href="#content"><?php esc_html_e( 'Skip to content', 'understrap' ); ?></a>

		<nav id="top-nav" class="navbar navbar-expand-md navbar-light bg-light justify-content-end d-none d-md-block">

			<?php if ( 'container' == $container ) : ?>
				<div class="container justify-content-end" >
			<?php endif; ?>

				<?php get_template_part( 'searchform' ); ?>

				<?php if ( has_nav_menu( 'topnav' ) ) {
					$menu_items = array();
					if ( ( $locations = get_nav_menu_locations() ) && isset( $locations[ 'topnav' ] ) ) {
						$menu = get_term( $locations[ 'topnav' ] );
						$menu_items = wp_get_nav_menu_items($menu->term_id);
						foreach ($menu_items as $menu_item) { ?>
							<a href="<?php echo $menu_item->url; ?>" class="btn <?php echo implode(" ", $menu_item->classes); ?>" target="<?php echo $menu_item->target; ?>"><?php _e( $menu_item->title, 'understrap' ); ?></a>
						<?php }
					}
				} ?>

				

			<?php if ( 'container' == $container ) : ?>
				</div><!-- .container -->
			<?php endif; ?>

		</nav>

		<nav id="main-nav" class="navbar navbar-expand-lg navbar-light bg-white d-none d-lg-block">

			<?php if ( 'container' == $container ) : ?>
				<div class="container" >
			<?php endif; ?>

				<!-- Your site title as branding in the menu -->
				<?php if ( ! has_custom_logo() ) { ?>

					<?php if ( is_front_page() && is_home() ) : ?>

						<h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>

					<?php else : ?>

						<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a>

					<?php endif; ?>


				<?php } else { ?>
					<?php the_custom_logo(); ?>
				<?php } ?><!-- end custom logo -->

				<!-- The WordPress Menu goes here -->
				<?php wp_nav_menu(
					array(
						'theme_location'  => 'primary',
						'container_class' => 'collapse navbar-collapse d-flex flex-grow-1 ml-md-5',
						'container_id'    => 'main-menu-wrapper',
						'menu_class'      => 'navbar-nav ml-auto w-100 justify-content-between',
						'fallback_cb'     => '',
						'menu_id'         => 'main-menu',
						'depth'           => 3,
						'walker'          => new Procede_WP_Bootstrap_Navwalker(),
					)
				); ?>

			<?php if ( 'container' == $container ) : ?>
				</div><!-- .container -->
			<?php endif; ?>

		</nav><!-- .site-navigation -->

		<nav id="mobile-nav" class="navbar sticky-top navbar-expand-lg navbar-light bg-white d-block d-lg-none">

			<div class="row justify-content-between align-items-center">

				<div class="col-6">

					<!-- Your site title as branding in the menu -->
					<?php if ( ! has_custom_logo() ) { ?>

						<?php if ( is_front_page() && is_home() ) : ?>

							<h1 class="navbar-brand mb-0"><a rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a></h1>

						<?php else : ?>

							<a class="navbar-brand" rel="home" href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" itemprop="url"><?php bloginfo( 'name' ); ?></a>

						<?php endif; ?>


					<?php } else { ?>
						<?php the_custom_logo(); ?>
					<?php } ?><!-- end custom logo -->

				</div>

				<div class="col-auto">

					<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#mobile-menu-wrapper" aria-controls="mobile-menu-wrapper" aria-expanded="false" aria-label="<?php esc_attr_e( 'Toggle navigation', 'understrap' ); ?>">
						<span class="navbar-toggler-icon"></span>
					</button>

				</div>

				<!-- The WordPress Menu goes here -->
				<?php wp_nav_menu(
					array(
						'theme_location'  => 'mobile',
						'container_class' => 'collapse navbar-collapse',
						'container_id'    => 'mobile-menu-wrapper',
						'menu_class'      => 'navbar-nav',
						'fallback_cb'     => '',
						'menu_id'         => 'mobile-menu',
						'depth'           => 2,
						'walker'          => new WP_Bootstrap_Navwalker(),
					)
				); ?>

		</nav><!-- .site-navigation -->

	</div><!-- #wrapper-navbar end -->
