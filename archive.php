<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

get_header();

$container = get_theme_mod( 'understrap_container_type' );

$post_type = get_post_type();

?>

<div class="wrapper" id="archive-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<?php if ( $post_type === 'post' ) { ?>

			<?php get_template_part( 'partials/partial', 'featured-post' ); ?>

		<?php } ?>

		<div class="row">

			<!-- Do the left sidebar check and opens the primary div -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<?php if ( have_posts() ) : ?>

				<!-- <div class="posts-loop-layout-wrapper">
					<div class="post-loop-layout">
						<a href="#posts-layout" class="post-layout-links" data-layout="list"><i class="fa fa-list" aria-hidden="true"></i></a>
						<a href="#posts-layout" class="post-layout-links" data-layout="grid"><i class="fa fa-th" aria-hidden="true"></i></a>
					</div>
				</div> -->

				<main class="site-main card-deck" id="main" data-cols="3">

						<?php /* Start the Loop */ ?>

						<?php while ( have_posts() ) : the_post(); ?>

							<?php get_template_part( 'loop-templates/content', 'card' ); ?>

						<?php endwhile; ?>


				</main><!-- #main -->

				<!-- The pagination component -->
				<?php understrap_pagination(); ?>

			<?php else : ?>

				<div class="row">

					<?php get_template_part( 'loop-templates/content', 'none' ); ?>

				</div>

			<?php endif; ?>

			<!-- Do the right sidebar check -->
			<?php get_template_part( 'global-templates/right-sidebar-check' ); ?>

		</div><!-- .row -->

	</div><!-- Container end -->

</div><!-- Wrapper end -->

<?php get_footer(); ?>
