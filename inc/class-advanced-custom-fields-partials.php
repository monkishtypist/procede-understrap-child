<?php
/*
  Class Name: Advanced Custom Fields (ACF) Partials
  Description: Renders ACF rows and partials
  Version: 1.0
  Author: Tim Spinks
  Author URI: https://www.parkerwhite.com/
*/

class Advanced_Custom_Fields_Partials {

	private $bytes = 4;
	private $container = 'container';
	private $key;
	private $repeater_field;

	public function __construct( $container = 'container' ) {
		$this->container = $container;

		add_filter( 'option_active_plugins',  array( $this, 'disable_acf_on_frontend' ),   10, 1 );
	}

	public function __get( $property ) {
		if ( property_exists( $this, $property ) ) {
			return $this->$property;
		}
	}

	public function __set( $property, $value ) {
		if ( property_exists( $this, $property ) ) {
			$this->$property = $value;
		}
		return $this;
	}

	/**
	 * Disable ACF on Frontend
	 *
	 * Why would we want to disable front-end ACF? For speed and to prevent things  
	 * from breaking if the plugin ever gets disabled. Check out these articles 
	 * [here](https://www.billerickson.net/code/disable-acf-frontend/)
	 * [here](https://www.billerickson.net/advanced-custom-fields-frontend-dependency/)
	 *
	 */
	public function disable_acf_on_frontend( $plugins ) {
		if ( is_admin() ) {
			return $plugins;
		}
		foreach ( $plugins as $i => $plugin ) {
			if ( 'advanced-custom-fields-pro/acf.php' == $plugin ) {
				unset( $plugins[$i] );
			}
		}
		return $plugins;
	}

	/**
	 * Generate rand str
	 */
	public function rand_str() {
		return bin2hex( openssl_random_pseudo_bytes( $this->bytes ) );
	}

	/**
	 * Get random Post ID
	 */
	public function get_rand_post( $post_type = false, $return_obj = false ) {

		$post_type = ( $post_type ? $post_type : get_post_type( get_the_ID() ) );

		$args = array( 
			'orderby'        => 'rand',
			'posts_per_page' => '1', 
			'post_type'      => $post_type
		);

		$loop = new WP_Query( $args );

		while ( $loop->have_posts() ) : $loop->the_post();
			$id = get_the_ID();
			if ( $return_obj )
				return get_post( $id );
			return $id;
		endwhile;
	}

	/**
	 * Iterate through Repeater/Flexible Content fields
	 *
	 * @param (str) $repeater_field    the repeater field name
	 */
	public function repeater( $repeater_field ) {
		$rows = get_post_meta( get_the_ID(), $repeater_field, true );
		if ( $rows ) {
			$this->repeater_field = $repeater_field;
			foreach ($rows as $key => $row) {
				$this->key = $key;
				$method = 'acf_partial_' . str_replace( '-', '_', $row );
				if ( method_exists( __CLASS__, $method ) ) {
					$this->$method();
				}
			}
		}
	}

	/**
	 * Get ACF field value
	 *
	 * @param (str) $field    the field name
	 * @param (bool) $esc     whether or not to escape the returned value (default = true)
	 *
	 * @return (str/array) $value
	 */
	public function get_field( $field, $esc = true ) {
		
		$value = get_post_meta( get_the_ID(), $field, true );

		if ( $esc ) {
			$value = esc_html( $value );
		}

		return $value;
	}

	/**
	 * Prints Page headers HTML
	 */
	public function acf_partial_page_header( $style = 'hero' ) {

		$container = $this->container;

		$post_id   = get_the_ID();

		if ( $style == 'hero' ) : ?>
		
		<section id="header-wrapper" class="wrapper <?php echo 'wrapper-' . $style; ?>">

			<?php echo get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'object-fit-cover' ) ); ?>

			<div id="header-hero-content-wrapper">

				<div class="<?php echo esc_attr( $container ); ?>" id="" tabindex="-1">

					<div class="row">

						<div class="col-12 col-md-8 col-xl-8 offset-md-2 offset-xl-2 text-center">

							<header class="page-header text-white">

								<?php if ( $this->get_field( 'page_title' ) ) { ?>
									<h1 class="page-title"><?php echo $this->get_field( 'page_title', false ); ?></h1>
								<?php } ?>

								<?php if ( $this->get_field( 'page_subtitle' ) ) { ?>
									<h2 class="page-subtitle"><?php echo $this->get_field( 'page_subtitle', false ); ?></h2>
								<?php } ?>

								<?php if ( $this->get_field( 'header_text' ) ) { ?>
									<div class="page-description"><?php echo $this->get_field( 'header_text', false ); ?></div>
								<?php } ?>

							</header><!-- .page-header -->

						</div>

					</div>

				</div><!-- .container -->

			</div><!-- #header-hero-content-wrapper -->

		</section>

		<?php endif;
	}

	/**
	 * Individual Rows
	 *
	 * The following methods are designed to be called individually from within
	 * the template file.
	 */

	/**
	 * The ACF partial template for Related Posts (card deck)
	 */
	public function acf_partial_related_posts( $repeater_field ) {

		$posts         = $this->get_field( $repeater_field, false );

		$post_type     = get_post_type();

		$read_more     = ( $post_type == 'post' ? get_home_url() . '/articles/' : get_post_type_archive_link( $post_type ) );

		if ( $posts ): 

			ob_start();

			?>

			<section class="wrapper related-posts-wrapper">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<div class="row card-deck">

						<?php for ($i = 0; $i < $posts; $i++) {

							$post_id       = esc_html( get_post_meta( get_the_ID(), $repeater_field . '_' . $i . '_post', true ) );

							if ( ! $post_id )
								$post_id   = $this->get_rand_post( $post_type );

							$post          = get_post( $post_id );
							$post_image    = get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'object-fit-cover' ) );

							?>

							<article class="card" id="post-<?php echo $post_id; ?>">

								<?php if ( $post_image ) : ?>

									<div class="card-header p-0">

										<?php echo $post_image; ?>

									</div>

								<?php endif; ?>

								<div class="card-body">

									<h5 class="card-title"><?php echo get_the_title( $post_id ); ?></h5>

									<?php if ( has_excerpt( $post_id ) ) { ?>

										<?php echo get_the_excerpt( $post_id ); ?>

									<?php } else { ?>

										<?php echo apply_filters( 'the_content', html_entity_decode( $post->post_content ) ); ?>

									<?php } ?>

								</div><!-- .card-body -->

								<div class="card-footer text-right">

									<?php // fareverse_read_more( $post ); ?>

								</div><!-- .card-footer -->

							</article><!-- #post-## -->

						<?php } ?>

					</div>

					<div class="pagination-banner d-flex align-items-center">

						<ul class="nav ml-auto justify-content-end">

							<li class="nav-item">

								<a class="next nav-link" href="<?php echo $read_more; ?>"><?php _e( 'View More', 'understrap' ); ?></a>

							</li>

						</ul>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for Additional Team Members (card deck)
	 */
	public function acf_partial_additional_team_members( $repeater_field ) {

		$posts         = $this->get_field( $repeater_field, false );

		$post_type     = get_post_type();

		$read_more     = get_home_url() . '/teams/leadership-team/';

		$term          = get_the_terms( get_the_ID(), 'cpt-teams' );

		$term_link     = get_term_link( $term[0] );

		if ( $posts ): 

			ob_start();

			?>

			<section id="section-additional-team-members-wrapper" class="wrapper bg-denim">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<div class="row card-deck">

						<?php for ($i = 0; $i < $posts; $i++) {

							$post_id       = esc_html( get_post_meta( get_the_ID(), $repeater_field . '_' . $i . '_post', true ) );

							if ( ! $post_id )
								$post_id   = $this->get_rand_post( $post_type );

							$post          = get_post( $post_id );
							$post_image    = get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'object-fit-cover' ) );

							$card_term     = get_the_terms( $post_id, 'cpt-teams' );

							$job_title     = $card_term[0]->name; // get_post_meta( $post_id, 'job-title', true );

							?>

							<article class="card" id="post-<?php echo $post_id; ?>">

								<?php if ( $post_image ) : ?>

									<div class="card-header p-0">

										<?php echo $post_image; ?>

									</div>

								<?php endif; ?>

								<div class="card-body">

									<?php the_title( '<h5 class="card-title">', '</h5>' ); ?>

									<?php echo $job_title; ?>

								</div><!-- .card-body -->

								<div class="card-footer text-right">

									<?php // fareverse_read_more( $post ); ?>

								</div><!-- .card-footer -->

							</article><!-- #post-## -->

						<?php } ?>

					</div>

					<div class="pagination-banner d-flex align-items-center">

						<ul class="nav ml-auto justify-content-end">

							<li class="nav-item">

								<a class="next nav-link" href="<?php echo $term_link; ?>"><?php _e( 'View More', 'understrap' ); ?></a>

							</li>

						</ul>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for CTA banners
	 */
	public function acf_partial_cta() {

		$term = get_queried_object();

		$show_cta = ( isset( $term->taxonomy ) ? get_term_meta( $term->term_id, 'include_cta', true ) : $this->get_field( 'include_cta', false ) );

		if ( $show_cta ) { 

			$cta             = ( isset( $term->taxonomy ) ? get_term_meta( $term->term_id, 'cta_content_call_to_action', true ) : $this->get_field( 'cta_content_call_to_action' ) );
			$supporting_text = ( isset( $term->taxonomy ) ? get_term_meta( $term->term_id, 'cta_content_supporting_text', true ) : $this->get_field( 'cta_content_supporting_text' ) );
			$button          = ( isset( $term->taxonomy ) ? get_term_meta( $term->term_id, 'cta_content_buttoncta_content_button', true ) : $this->get_field( 'cta_content_button', false ) );
			$image_id        = ( isset( $term->taxonomy ) ? get_term_meta( $term->term_id, 'cta_content_image', true ) : $this->get_field( 'cta_content_image', false ) );
			$image_url       = wp_get_attachment_url( $image_id );
			$image_alt       = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

			ob_start();

			?>

			<section id="section-cta" class="bg-light-gray">

				<div class="row m-0">

					<div class="col-12 col-md-6 p-0">

						<img src="<?php echo $image_url; ?>" alt="<?php _e( $image_alt, 'understrap' ); ?>" class="object-fit-cover">

					</div>

					<div class="col-12 col-md-6 p-0">

						<div class="container-split-right">

							<div class="row">

								<div class="col-12 col-md-10">

									<h2 class="cta"><?php esc_html_e( $cta, 'understrap' ); ?></h2>

									<p><?php esc_html_e( $supporting_text, 'understrap' ); ?></p>

									<a href="<?php echo $button['url']; ?>" class="btn btn-secondary" target="<?php echo $button['target']; ?>"><?php _e( ( $button['title'] ? esc_html( $button['title'] ) : 'Learn More' ), 'understrap' ); ?></a>

								</div>

							</div>

						</div>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		}

		return;

	}

	/**
	 * Repeater Rows
	 *
	 * The following methods are designed to be called from within the 1page_builder1
	 * repeater included in certain template files.
	 */

	/**
	 * The ACF partial template for Posts Card Decks
	 */
	public function acf_partial_posts_card_deck() {

		$key = $this->key;

		$rand_str          = $this->rand_str();

		$css_class         = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color     = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$section_title     = $this->get_field( $this->repeater_field . '_' . $key . '_section_title' );
		$section_intro     = $this->get_field( $this->repeater_field . '_' . $key . '_section_intro', false );
		$read_more         = $this->get_field( $this->repeater_field . '_' . $key . '_read_more' );
		$cards             = $this->get_field( $this->repeater_field . '_' . $key . '_cards', false );

		if ( $cards ): 

			ob_start();

			?>

			<section class="section-card-deck wrapper <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<div class="row">

						<div class="col">

							<h3 class="section-title"><?php _e( $section_title, 'understrap' ); ?></h3>

							<?php echo apply_filters( 'the_content', $section_intro ); ?>

						</div>

					</div>

					<div class="row card-deck" data-cols="<?php echo $cards; ?>">

						<?php for ($i = 0; $i < $cards; $i++) {

							$post_id      = esc_html( get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_post', true ) );
							$post         = get_post( $post_id );
							$post_image   = get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'object-fit-cover' ) );

							$link_text    = esc_html( get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_link_text', true ) );

							?>

							<article class="card" id="post-<?php echo $post_id; ?>">

								<?php if ( $post_image ) : ?>

									<div class="card-header">

										<?php echo $post_image; ?>

									</div>

								<?php endif; ?>

								<div class="card-body">

									<h5 class="card-title"><?php echo get_the_title( $post_id ); ?></h5>

									<?php if ( has_excerpt( $post_id ) ) { ?>

										<?php echo get_the_excerpt( $post_id ); ?>

									<?php } else { ?>

										<?php echo apply_filters( 'the_content', html_entity_decode( $post->post_content ) ); ?>

									<?php } ?>

								</div><!-- .card-body -->

								<div class="card-footer text-center">

									<a href="<?php echo get_permalink( $post_id ); ?>" class="btn btn-secondary"><?php echo ( $link_text ? $link_text : __( 'Read More' ) ); ?></a>

								</div><!-- .card-footer -->

							</article><!-- #post-## -->

						<?php } ?>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for Team Member Card Decks
	 */
	public function acf_partial_team_card_deck() {

		$key = $this->key;

		$rand_str      = $this->rand_str();

		$css_class     = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$section_title = $this->get_field( $this->repeater_field . '_' . $key . '_section_title' );
		$section_intro = $this->get_field( $this->repeater_field . '_' . $key . '_section_intro', false );
		$read_more     = $this->get_field( $this->repeater_field . '_' . $key . '_read_more', false ); // gets Term ID for team
		$cards         = $this->get_field( $this->repeater_field . '_' . $key . '_cards', false );

		if ( $cards ): 

			ob_start();

			?>

			<section class="section-card-deck wrapper <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<div class="row">

						<div class="col">

							<h3 class="section-title"><?php _e( $section_title, 'understrap' ); ?></h3>

							<?php echo apply_filters( 'the_content', $section_intro ); ?>

						</div>

					</div>

					<div class="row card-deck" data-cols="<?php echo $cards; ?>">

						<?php for ($i = 0; $i < $cards; $i++) {

							$post_id      = esc_html( get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_post', true ) );
							$post         = get_post( $post_id );
							$post_image   = get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'object-fit-cover' ) );

							$job_title    = esc_html( get_post_meta( $post_id, 'job-title', true ) );

							?>

							<article class="card" id="post-<?php echo $post_id; ?>">

								<?php if ( $post_image ) : ?>

									<div class="card-header p-0">

										<?php echo $post_image; ?>

									</div>

								<?php endif; ?>

								<div class="card-body">

									<h5 class="card-title"><?php echo get_the_title( $post_id ); ?></h5>

									<?php echo apply_filters( 'the_content', $job_title ); ?>

								</div><!-- .card-body -->

								<div class="card-footer text-right">

									<?php // fareverse_read_more( $post ); ?>

								</div><!-- .card-footer -->

							</article><!-- #post-## -->

						<?php } ?>

					</div>

					<?php if ( $read_more ) { ?>

						<div class="row justify-content-center">

							<div class="col-auto">

								<a href="<?php echo get_tag_link( $read_more ); ?>" alt="<?php _e( 'See all Team Members', 'understrap' ); ?>" class="btn btn-secondary"><?php _e( 'See All', 'understrap' ); ?></a>

							</div>

						</div>

					<?php } ?>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for Testimonials Card Group
	 */
	public function acf_partial_testimonials_card_group() {

		$key = $this->key;

		$rand_str      = $this->rand_str();

		$css_class     = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$section_title = $this->get_field( $this->repeater_field . '_' . $key . '_section_title' );
		$section_intro = $this->get_field( $this->repeater_field . '_' . $key . '_section_intro', false );
		// $read_more     = $this->get_field( $this->repeater_field . '_' . $key . '_read_more', false ); // gets Term ID for team
		$cards         = $this->get_field( $this->repeater_field . '_' . $key . '_cards', false );

		if ( $cards ): 

			ob_start();

			?>

			<section id="section-testimonials-card-group-<?php echo $rand_str; ?>-wrapper" class="section-testimonials-card-group wrapper <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<div class="row">

						<div class="col text-center">

							<h3 class="section-title"><?php _e( $section_title, 'understrap' ); ?></h3>

							<?php echo apply_filters( 'the_content', $section_intro ); ?>

						</div>

					</div>

					<div class="card-group" data-cols="<?php echo $cards; ?>">

						<?php for ($i = 0; $i < $cards; $i++) {

							$post_id      = esc_html( get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_post', true ) );
							$post         = get_post( $post_id );

							?>

							<article class="card" id="post-<?php echo $post_id; ?>">

								<div class="card-body">

									<?php echo apply_filters( 'the_content', $post->post_content ); ?>

								</div><!-- .card-body -->

								<div class="card-footer">

									<?php echo get_the_title( $post_id ); ?>

								</div><!-- .card-footer -->

							</article><!-- #post-## -->

						<?php } ?>

					</div>

					<div class="row justify-content-center">

						<div class="col-auto">

							<a href="<?php echo get_post_type_archive_link( 'cpt-testimonials' ); ?>" class="btn btn-tertiary"><?php _e( 'View All Testimonials', 'understrap' ); ?></a>

						</div>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for multi-column rows
	 */
	public function acf_partial_row_w_cols() {

		$key = $this->key;

		$rand_str          = $this->rand_str();

		$section_css_class = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color     = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$section_title     = $this->get_field( $this->repeater_field . '_' . $key . '_section_title', false );
		$row_css_class     = $this->get_field( $this->repeater_field . '_' . $key . '_row_css_class' );
		$columns           = $this->get_field( $this->repeater_field . '_' . $key . '_columns', false );
		$cols_per_row      = $this->get_field( $this->repeater_field . '_' . $key . '_columns_per_row', false );
		$cols_class        = ( $cols_per_row > 0 ? 'col-md-' . ( 12 / $cols_per_row ) : 'col-md-auto' );

		if ( $columns ): 

			ob_start();

			?>

			<section class="section-row-w-columns <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $section_css_class ) ); ?>">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<?php if ( $section_title ) { ?>

						<div class="row justify-content-center">

							<div class="col-12 text-center">

								<h2 class="section-title"><?php echo $section_title; ?></h2>

							</div>

						</div>

					<?php } ?>

					<div class="row <?php echo ( esc_html( $row_css_class ) ); ?>">

						<?php for ($i = 0; $i < $columns; $i++) {

							$content = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_content' );
							$col_css = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_css_class' );

							?>

							<div class="col-12 <?php echo $cols_class; ?> <?php echo $col_css; ?>">

								<?php echo apply_filters( 'the_content', html_entity_decode( $content ) ); ?>

							</div>

						<?php } ?>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for row with split image/content cols
	 */
	public function acf_partial_row_w_split() {

		$key               = $this->key;

		$rand_str          = $this->rand_str();

		$section_css_class = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color     = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$columns           = $this->get_field( $this->repeater_field . '_' . $key . '_columns', false );
		$cols_class        = ( $columns[0] == 'content_column' ? 'container-split-left' : 'container-split-right' );

		if ( $columns ): 

			ob_start();

			?>

			<section class="section-row-split <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo $css_class; ?>">

				<div class="row m-0">

					<?php foreach ( $columns as $i => $column ) { ?>

						<?php if ( 'content_column' == $column ) {

							$content  = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_content' );
							$col_css  = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_css_class' );

							?>

							<div class="col-12 col-md-6 p-0 <?php echo $col_css; ?>">

								<div class="<?php echo $cols_class; ?> h-100">

									<div class="row h-100 align-items-center">

										<div class="col-12 col-md-10 <?php echo ( $i == 0 ? 'offset-md-2' : '' ); ?>">

											<?php echo apply_filters( 'the_content', html_entity_decode( $content ) ); ?>

										</div>

									</div>

								</div>

							</div>

						<?php } elseif ( 'image_column' == $column ) {

							$image_id  = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_image' );
							$image_url = wp_get_attachment_url( $image_id );
							$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );

							?>

							<div class="col-12 col-md-6 p-0">
								
								<img src="<?php echo $image_url; ?>" alt="<?php _e( $image_alt, 'understrap' ); ?>" class="object-fit-cover">

							</div>

						<?php } ?>

					<?php } ?>

				</div>

			</section>

			<?php 

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for sliders
	 */
	public function acf_partial_content_slider() {

		$key = $this->key;

		$rand_str      = $this->rand_str();

		$css_class     = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$slides        = $this->get_field( $this->repeater_field . '_' . $key . '_slides' );

		if ( $slides ):

			$slide_content    = array();
			$slide_images     = array();
			$slide_indicators = array();

			for ($i = 0; $i < $slides; $i++) { 

				$active    = ( $i == 0 ? 'active' : '' );
				$content   = $this->get_field( $this->repeater_field . '_' . $key . '_slides_' . $i . '_content', false );
				$image_id  = $this->get_field( $this->repeater_field . '_' . $key . '_slides_' . $i . '_image' );
				$image_url = wp_get_attachment_url( $image_id );
				$image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true);

				$slide_content[]    = sprintf( '<div class="carousel-item %1$s">%2$s</div>', $active, apply_filters( 'the_content', $content ) );

				$slide_indicators[] = sprintf( '<li data-target=".carousel_%1$s" data-slide-to="%2$d" class="carousel-indicator_%3$s %4$s"></li>', $rand_str, $i, $rand_str, $active );

				$slide_images[]     = sprintf( '<div class="carousel-item %1$s"><img class="d-block w-100" src="%2$s" alt="%3$s"></div>', $active, $image_url, $image_alt );
			}

			ob_start();

			?>

			<section class="section-slider-content <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<div class="row">

						<div class="col-12 col-md-6">

							<div id="carousel_1_<?php echo $rand_str; ?>" class="carousel slide carousel_<?php echo $rand_str; ?> carousel-content" data-ride="false">

								<ol class="carousel-indicators">

									<?php echo implode( '', $slide_indicators ); ?>

								</ol>

								<div class="carousel-inner">

									<?php echo implode( '', $slide_content ); ?>

								</div>

							</div>

						</div>

						<div class="col-12 col-md-6">

							<div id="carousel_2_<?php echo $rand_str; ?>" class="carousel slide carousel_<?php echo $rand_str; ?> carousel-images" data-ride="false">

								<div class="carousel-inner">

									<?php echo implode( '', $slide_images ); ?>

								</div>

								<a class="carousel-control-prev" href="#carousel_2_<?php echo $rand_str; ?>" role="button" data-slide="prev">

									<span class="carousel-control-prev-icon" aria-hidden="true"></span>
									<span class="sr-only">Previous</span>

								</a>

								<a class="carousel-control-next" href="#carousel_2_<?php echo $rand_str; ?>" role="button" data-slide="next">

									<span class="carousel-control-next-icon" aria-hidden="true"></span>
									<span class="sr-only">Next</span>

								</a>

							</div>

						</div>

					</div>

				</div>

				<script type="text/javascript">
					jQuery('.carousel-control-next').on('click', function(e) {
						e.preventDefault();
						jQuery('.carousel_<?php echo $rand_str; ?>').carousel('next');
					});
					jQuery('.carousel-control-prev').on('click', function(e) {
						e.preventDefault();
						jQuery('.carousel_<?php echo $rand_str; ?>').carousel('prev');
					});
					jQuery('.carousel-indicator_<?php echo $rand_str; ?>').on('click', function(e) {
						e.preventDefault();
						var to = parseInt(jQuery(this).attr("data-slide-to"));
						jQuery('.carousel_<?php echo $rand_str; ?>').carousel(to);
					});
				</script>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for Testimonial sliders
	 */
	public function acf_partial_testimonial_slider() {

		$key = $this->key;

		$rand_str        = $this->rand_str();

		$css_class       = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_color   = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$section_title   = $this->get_field( $this->repeater_field . '_' . $key . '_section_title', false );
		$slides          = $this->get_field( $this->repeater_field . '_' . $key . '_slides', false );

		$show_controls   = $this->get_field( $this->repeater_field . '_' . $key . '_carousel_controls', false );
		$show_indicators = $this->get_field( $this->repeater_field . '_' . $key . '_carousel_indicators', false );

		$interval        = $this->get_field( $this->repeater_field . '_' . $key . '_carousel_interval', false );

		if ( $slides ):

			$slide_content    = array();
			$slide_indicators = array();

			for ($i = 0; $i < $slides; $i++) { 

				$active              = ( $i == 0 ? 'active' : '' );
				$testimonial_id      = $this->get_field( $this->repeater_field . '_' . $key . '_slides_' . $i . '_testimonials', false );
				$testimonial         = get_post( $testimonial_id );
				$testimonial_content = apply_filters( 'the_content', $testimonial->post_content );
				$testimonial_image   = get_the_post_thumbnail( $testimonial_id );

				$slide_content[]    = sprintf( '<div class="carousel-item %1$s">
						<div class="">
							<blockquote class="blockquote">%2$s<footer class="blockquote-footer">%3$s %4$s</footer></blockquote>
						</div>
					</div>', $active, $testimonial_content, $testimonial_image, get_the_title( $testimonial_id ) );

				$slide_indicators[] = sprintf( '<li data-target="#carousel_%1$s" data-slide-to="%2$d" class="carousel-indicator %3$s"></li>', $rand_str, $i, $active );

			}

			ob_start();

			?>

			<section class="section-slider-testimonial <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">

				<div class="<?php echo esc_attr( $this->container ); ?>">

					<?php if ( $section_title ) { ?>

						<div class="row justify-content-center">

							<div class="col-12 col-md-10 col-xl-8 text-center">

								<h2 class="section-title"><?php echo $section_title; ?></h2>

							</div>

						</div>

					<?php } ?>

					<div class="row justify-content-center">

						<div class="col-12 col-md-10 col-xl-8">

							<div id="carousel_<?php echo $rand_str; ?>" class="carousel slide carousel_<?php echo $rand_str; ?>" data-ride="false" data-interval="<?php echo ( $interval ? $interval : false ); ?>">

								<?php if ( $show_indicators ) { ?>
									<ol class="carousel-indicators">
										<?php echo implode( '', $slide_indicators ); ?>
									</ol>
								<?php } ?>

								<div class="carousel-inner row text-center">

									<?php echo implode( '', $slide_content ); ?>

								</div>

								<?php if ( $show_controls ) { ?>
									<a class="carousel-control-prev" href="#carousel_<?php echo $rand_str; ?>" role="button" data-slide="prev">
										<span class="carousel-control-prev-icon" aria-hidden="true"></span>
										<span class="sr-only"><?php _e( 'Previous' ); ?></span>
									</a>
									<a class="carousel-control-next" href="#carousel_<?php echo $rand_str; ?>" role="button" data-slide="next">
										<span class="carousel-control-next-icon" aria-hidden="true"></span>
										<span class="sr-only"><?php _e( 'Next' ); ?></span>
									</a>
								<?php } ?>

							</div>

						</div>

					</div>

					<div class="row justify-content-center">

						<div class="col-12 text-center">

							<a href="<?php echo get_permalink( get_page_by_path( 'testimonials' ) ); ?>" class="btn btn-outline-primary"><?php _e( 'View More Testimonials' ); ?></a>

						</div>

					</div>

				</div>

			</section>

			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for Row break
	 */
	public function acf_partial_row_break() {

		$key = $this->key;

		$rand_str      = $this->rand_str();

		$section_color = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$line_color    = $this->get_field( $this->repeater_field . '_' . $key . '_line_color', false );

		ob_start();

		?>

		<section class="section-row-break <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> py-0">

			<div class="<?php echo esc_attr( $this->container ); ?>">

				<div class="row justify-content-center">

					<div class="col-3">

						<hr class="<?php echo $line_color; ?>" />

					</div>

				</div>

			</div>

		</section>

		<?php

		echo ob_get_clean();

		return;

	}

}
