<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$container = get_theme_mod( 'understrap_container_type' );
?>

<footer class="site-footer">

	<div class="wrapper" id="wrapper-footer">

		<div class="<?php echo esc_attr( $container ); ?>">

			<div class="row justify-content-between">

				<div class="col-12 col-md">

					<?php the_custom_logo(); ?>

					<h5 class="tagline"><?php echo str_replace( '. ', ".<br />", get_bloginfo('description') ); ?></h5>

				</div>

				<?php get_template_part( 'sidebar-templates/sidebar', 'footer' ); ?>

			</div>

		</div>

	</div>

	<div class="wrapper" id="wrapper-colophon">

		<div class="<?php echo esc_attr( $container ); ?>">

			<div class="row justify-content-center align-items-center">

				<div class="col text-center">

					<div class="site-info">

						<?php understrap_site_info(); ?>

					</div>

				</div>

			</div>

		</div>

	</div>

</footer>

</div><!-- #page we need this extra closing tag here -->

<?php wp_footer(); ?>

</body>

</html>

