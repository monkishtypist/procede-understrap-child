<?php
/**
 * Single post partial template.
 *
 * @package understrap
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<article <?php post_class(); ?> id="post-<?php the_ID(); ?>">

	<div class="breadcrumbs" typeof="BreadcrumbList" vocab="https://schema.org/">
		<?php
		if ( function_exists( 'bcn_display' ) ) {
			bcn_display();
		}?>
	</div>

	<header class="entry-header">

		<?php echo get_the_post_thumbnail( $post->ID, 'large', array( 'class' => 'img-fluid' ) ); ?>

		<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

	</header><!-- .entry-header -->

	<div class="entry-content">

		<?php the_content(); ?>

	</div><!-- .entry-content -->

	<footer class="entry-footer">

		<div class="row">

			<div class="col"><?php understrap_entry_footer(); ?></div>

			<div class="col-12 col-md-auto">
				<span class="social-icons">
					<a href="http://www.linkedin.com/shareArticle?mini=true&url=<?php echo get_permalink(); ?>&title=<?php echo get_the_title(); ?>&source=<?php echo get_permalink(); ?>" target="_blank"><i class="fab fa-linkedin-in"></i></a>
					<!-- <a href="https://www.youtube.com/channel/UCLV7Lu6pQ5bD9UaSvTYTMBg" target="_blank"><i class="fab fa-youtube"></i></a> -->
					<a href="https://twitter.com/intent/tweet?text=<?php echo urlencode( get_the_title() ); ?>&amp;url=<?php echo urlencode( get_permalink() ); ?>" target="_blank"><i class="fab fa-twitter"></i></a>
					<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode( get_permalink() ); ?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore"><i class="fab fa-facebook-f"></i></a>
				</span>
			</div>

		</div>

	</footer><!-- .entry-footer -->

</article><!-- #post-## -->
