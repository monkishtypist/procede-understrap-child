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

$container   = get_theme_mod( 'understrap_container_type' );

$upload_dir = wp_upload_dir();

?>

<section id="header-wrapper" class="wrapper <?php echo 'wrapper-' . $style; ?>">

	<img width="1920" height="435" src="<?php echo $upload_dir['baseurl']; ?>/2018/10/WHATS-NEW-HERO.png" class="object-fit-cover wp-post-image" alt="" sizes="(max-width: 1920px) 100vw, 1920px">

	<div id="header-hero-content-wrapper">
	
		<div class="<?php echo esc_attr( $container ); ?>" id="" tabindex="-1">

			<div class="row">

				<div class="col-12 col-md-8 col-xl-8 offset-md-2 offset-xl-2 text-center">

					<header class="page-header">

						<h1 class="page-title"><?php echo sprintf( '%1$s <span class="text-primary">(%2$s)</span> %3$s.', __( 'The latest', 'understrap' ), __( 'and greatest', 'understrap' ), __( 'news, features and announcements', 'understrap' ) ); ?></h1>

					</header><!-- .page-header -->

				</div>

			</div>

		</div><!-- .container -->

	</div><!-- #header-hero-content-wrapper -->

</section>

<div class="wrapper" id="home-wrapper">

	<div class="<?php echo esc_attr( $container ); ?>" id="content" tabindex="-1">

		<?php
		$cat = get_category_by_slug( 'featured' );
		$args = array(
			'numberposts' => 1,
			'offset' => 0,
			'category' => ( isset($cat->term_id) ? $cat->term_id : '' ),
			'orderby' => 'post_date',
			'order' => 'DESC',
			'include' => '',
			'exclude' => '',
			'meta_key' => '',
			'meta_value' =>'',
			'post_type' => 'post',
			'post_status' => 'publish',
			'suppress_filters' => true
		);
		$featured_posts = wp_get_recent_posts( $args, OBJECT );
		?>

		<?php foreach ($featured_posts as $featured_post) : $post_object = get_post( $featured_post->ID ); ?>

			<div class="row row-featured mb-4">
				
				<?php if ( has_post_thumbnail( $featured_post->ID ) ) { ?>
					<div class="col-12 col-lg-7 order-12">
						<?php echo get_the_post_thumbnail( $featured_post->ID, 'full', array( 'class' => 'object-fit-cover' ) ); ?>
					</div>
				<?php } ?>

				<div class="col-12 col-lg-5 order-1">
					<h2><?php echo get_the_title( $featured_post->ID ); ?></h2>
					<?php echo apply_filters( 'the_content', wp_trim_words( $post_object->post_content, 50, '...' ) ); ?>
					<p class="text-right"><a href="<?php echo get_permalink( $featured_post->ID ); ?>" class="btn btn-primary"><?php echo __( 'Read More', 'understrap' ); ?></a></p>
				</div>

			</div>

		<?php endforeach; ?>

		<div class="row">

			<!-- Do the left sidebar check and opens the primary div -->
			<?php get_template_part( 'global-templates/left-sidebar-check' ); ?>

			<?php if ( have_posts() ) : ?>

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
