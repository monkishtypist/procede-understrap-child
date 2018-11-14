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

		// var_dump(get_post_meta( $post_id ));

		$css_classes_page_header_row_css    = get_post_meta( $post_id, 'css_classes_page_header_row_css', true );
		$css_classes_page_header_column_css = get_post_meta( $post_id, 'css_classes_page_header_column_css', true );

		if ( $style && $style !== 'none' ) : ?>
		<section id="header-wrapper" class="wrapper <?php echo 'wrapper-' . $style; ?>">
			<?php if ( $style == 'hero' ) : ?>
				<?php echo get_the_post_thumbnail( $post_id, 'full', array( 'class' => 'object-fit-cover' ) ); ?>
				<div id="header-hero-content-wrapper">
			<?php else : ?>
				<div id="header-content-wrapper">
			<?php endif; ?>
				<div class="<?php echo esc_attr( $container ); ?>" id="" tabindex="-1">
					<div class="row <?php echo ( $css_classes_page_header_row_css ? $css_classes_page_header_row_css : 'align-items-center justify-content-center' ); ?>">
						<div class="col-12 text-center <?php echo ( $css_classes_page_header_column_css ? $css_classes_page_header_column_css : 'col-md-8 col-lg-7 col-xl-7' ); ?>">
							<header class="page-header">
								<?php if ( $this->get_field( 'page_title' ) ) { ?>
									<h1 class="page-title"><?php echo $this->get_field( 'page_title', false ); ?></h1>
								<?php } ?>
								<?php if ( $this->get_field( 'page_subtitle' ) ) { ?>
									<h2 class="page-subtitle"><?php echo $this->get_field( 'page_subtitle', false ); ?></h2>
								<?php } ?>
								<?php if ( $this->get_field( 'header_text' ) ) { ?>
									<div class="page-description"><?php echo apply_filters( 'the_content', html_entity_decode( do_shortcode( $this->get_field( 'header_text', false ) ) ) ); ?></div>
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
		$section_id        = $this->get_field( $this->repeater_field . '_' . $key . '_section_id' );
		$section_color     = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$section_title     = $this->get_field( $this->repeater_field . '_' . $key . '_section_title' );
		$section_intro     = $this->get_field( $this->repeater_field . '_' . $key . '_section_intro', false );
		$read_more         = $this->get_field( $this->repeater_field . '_' . $key . '_read_more' );
		$cards             = $this->get_field( $this->repeater_field . '_' . $key . '_cards', false );

		if ( $cards ): 

			ob_start();

			?>
			<section class="section-card-deck wrapper <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">
				<a class="anchor" name="anchor-<?php echo $section_id; ?>"></a>
				<div class="<?php echo esc_attr( $this->container ); ?>">
					<div class="row">
						<div class="col">
							<h3 class="section-title"><?php _e( $section_title, 'understrap' ); ?></h3>
							<?php echo apply_filters( 'the_content', html_entity_decode( $section_intro ) ); ?>
						</div>
					</div>
					<div class="card-deck" data-cols="<?php echo $cards; ?>">
						<?php for ($i = 0; $i < $cards; $i++) {

							$use_custom_content = get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_use_custom_content', true );

							$card_img_top = false;
							$card_title   = false;
							$card_body    = false;
							$link_title   = false;
							$link_url     = false;
							$link_target  = '_self';

							if ( $use_custom_content ) {
								$post_id       = $i;
								$card_img_id   = get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_card_image', true );
								$card_img_src  = wp_get_attachment_image_src( $card_img_id, 'large' );
								$card_img_top  = $card_img_src[0];
								$card_title    = get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_card_title', true );
								$card_body     = get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_card_body', true );
								$card_body     = apply_filters( 'the_content', html_entity_decode( $card_body ) );
								$link          = get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_card_link', true );
								$link_title    = $link['title'];
								$link_url      = $link['url'];
								$link_target   = $link['target'];
							} else {
								$post_id       = esc_html( get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_post', true ) );
								$post          = get_post( $post_id );

								if ( has_post_thumbnail( $post_id ) ) {
									$card_img_top = get_the_post_thumbnail_url( $post_id, 'large' );
								}

								$card_title    = get_the_title( $post_id );

								$card_body     = ( has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : apply_filters( 'the_content', html_entity_decode( $post->post_content ) ) );

								$link_title    = esc_html( get_post_meta( get_the_ID(), $this->repeater_field . '_' . $key . '_cards_' . $i . '_link_text', true ) );
								$link_url      = get_permalink( $post_id );
							}

							?>
							<article class="card" id="post-<?php echo $post_id; ?>">
								<?php if ( $card_img_top ) : ?>
									<div class="card-header" data-img="<?php echo $card_img_top; ?>">
										<div class="card-header__img-wrapper">
											<img src="<?php echo $card_img_top; ?>" alt="" class="object-fit-cover card-img-top">
										</div>
									</div>
								<?php endif; ?>
								<div class="card-body">
									<?php if ( $card_title ) { ?>
										<h5 class="card-title"><?php echo $card_title; ?></h5>
									<?php } ?>
									<?php echo $card_body; ?>
								</div><!-- .card-body -->
								<div class="card-footer text-center">
									<a href="<?php echo $link_url; ?>" class="btn btn-secondary" target="<?php echo $link_target; ?>"><?php echo ( $link_title ? $link_title : __( 'Read More' ) ); ?></a>
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
	 * Repeater Rows
	 *
	 * The following methods are designed to be called from within the 1page_builder1
	 * repeater included in certain template files.
	 */

	/**
	 * The ACF partial template for Posts Card Decks
	 */
	public function acf_partial_related_posts() {

		$key = $this->key;

		$rand_str          = $this->rand_str();

		$related_posts     = $this->get_field( 'related_posts', false );

		if ( $related_posts > 0 ): 

			ob_start();

			?>
			<section class="section-card-deck wrapper">
				<h2><?php _e( 'Related Posts' ); ?></h2>
				<div class="card-deck" data-cols="<?php echo $related_posts; ?>">
					<?php for ($i = 0; $i < $related_posts; $i++) {

						$post_id       = get_post_meta( get_the_ID(), 'related_posts_' . $i . '_related_post', true );
						$post          = get_post( $post_id );

						$card_img_top  = false;
						if ( has_post_thumbnail( $post_id ) ) {
							$card_img_top = get_the_post_thumbnail_url( $post_id, 'large' );
						}

						$card_title    = get_the_title( $post_id );

						$card_body     = ( has_excerpt( $post_id ) ? get_the_excerpt( $post_id ) : apply_filters( 'the_content', html_entity_decode( wp_trim_words( $post->post_content ) ) ) );

						$link_url      = get_permalink( $post_id );

					?>
						<article class="card" id="post-<?php echo $post_id; ?>">
							<?php if ( $card_img_top ) : ?>
								<div class="card-header" data-img="<?php echo $card_img_top; ?>">
									<div class="card-header__img-wrapper">
										<img src="<?php echo $card_img_top; ?>" alt="" class="object-fit-cover card-img-top">
									</div>
								</div>
							<?php endif; ?>
							<div class="card-body">
								<?php if ( $card_title ) { ?>
									<h3 class="card-title"><?php echo $card_title; ?></h3>
								<?php } ?>
								<?php echo $card_body; ?>
							</div><!-- .card-body -->
							<div class="card-footer text-center">
								<a href="<?php echo $link_url; ?>" class="btn btn-secondary" target="<?php echo $link_target; ?>"><?php _e( 'Read More' ); ?></a>
							</div><!-- .card-footer -->
						</article><!-- #post-## -->
					<?php } ?>
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
		$section_id        = $this->get_field( $this->repeater_field . '_' . $key . '_section_id' );
		$background_image  = $this->get_field( $this->repeater_field . '_' . $key . '_section_background_image' );
		$row_css_class     = $this->get_field( $this->repeater_field . '_' . $key . '_row_css_class' );
		$split_background  = $this->get_field( $this->repeater_field . '_' . $key . '_split_background', false );
		$columns           = $this->get_field( $this->repeater_field . '_' . $key . '_columns', false );
		$cols_per_row      = $this->get_field( $this->repeater_field . '_' . $key . '_columns_per_row', false );
		$col_widths        = array( 'col-12' );

		if ( $background_image ) {
			$section_bg_img = wp_get_attachment_image_src( $background_image, 'full' );
			$section_css_class .= " section-w-background-image";
			$section_color = 'transparent';
		}

		if ( $split_background ) {
			$col_widths[]  = 'col-split';
			$col_widths[]  = 'col-split-md-' . ( 12 / $cols_per_row );
		} else {
			$col_widths[]  = ( $cols_per_row > 0 ? 'col-md-' . ( 12 / $cols_per_row ) : 'col-md-auto' );
		}

		if ( $columns ): 

			ob_start();

			?>
			<section class="section-row-w-columns <?php echo ( $split_background ? 'section-split-cols' : '' ); ?> <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $section_css_class ) ); ?>" id="<?php echo ( $section_id ? $section_id : '' ); ?>">
				<a class="anchor" name="anchor-<?php echo $section_id; ?>"></a>
				<?php if ( $background_image ) { ?>
					<div class="object-fit__img-wrapper" data-img="<?php echo $section_bg_img[0]; ?>">
						<img src="<?php echo $section_bg_img[0]; ?>" class="object-fit-cover section-background-image">
					</div>
					<div class="background-image-overlay">
				<?php } ?>
					<?php if ( $split_background ) { ?>
						<div class="container-fluid"><!-- .container -->
					<?php } else { ?>
						<div class="<?php echo esc_attr( $this->container ); ?>"><!-- .container -->
					<?php } ?>
						<?php if ( $section_title && ! $split_background ) { ?>
							<div class="row justify-content-center">
								<div class="col-12 text-center">
									<h2 class="section-title"><?php echo $section_title; ?></h2>
								</div>
							</div>
						<?php } ?>
						<div class="row <?php echo ( esc_html( $row_css_class ) ); ?>" data-cols="<?php echo $cols_per_row; ?>">
							<?php for ( $i = 0; $i < $columns; $i++ ) {

								$content       = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_content' );
								$col_css       = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_css_class' );
								$col_color     = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_column_color' );
								$col_image     = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_column_image' );
								$overwrite_css = $this->get_field( $this->repeater_field . '_' . $key . '_columns_' . $i . '_overwrite_css' );
								$col_style     = null;

								if ( $col_image ) {
									$image_src = wp_get_attachment_image_src( $col_image, 'full' );
									$col_style = sprintf( 'style="background-image:url(%1$s);background-repeat:no-repeat;background-size:cover;background-position:center;"', $image_src[0] );
								}

								if ( $overwrite_css && ! $split_background )
									$col_widths = array();

								if ( $col_color && $split_background )
									$col_css .= ' bg-' . $col_color;

								if ( $cols_per_row > 0 && $split_background ) :
									if ( ( $i + 1 ) % $cols_per_row === 0 )
										$col_css .= ' col-split-end';
									if ( $i % $cols_per_row === 0 )
										$col_css .= ' col-split-start';
								endif;

								?>
								<div class="<?php echo implode( " ", $col_widths ); ?> <?php echo $col_css; ?>" <?php echo $col_style; ?>>
									<?php echo apply_filters( 'the_content', html_entity_decode( $content ) ); ?>
								</div>
							<?php } ?>
						</div>
					</div><!-- /.container -->
				<?php if ( $background_image ) { ?>
					</div><!-- /.background-image-overlay -->
				<?php } ?>
			</section>
			<?php

			echo ob_get_clean();

		endif;

		return;

	}

	/**
	 * The ACF partial template for Posts sliders
	 */
	public function acf_partial_posts_slider() {

		$key = $this->key;

		$rand_str        = $this->rand_str();

		$css_class       = $this->get_field( $this->repeater_field . '_' . $key . '_css_class' );
		$section_id      = $this->get_field( $this->repeater_field . '_' . $key . '_section_id' );
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

				$active       = ( $i == 0 ? 'active' : '' );
				$is_custom    = $this->get_field( $this->repeater_field . '_' . $key . '_slides_' . $i . '_custom_content', false );

				if ( $is_custom ) {
					$content         = $this->get_field( $this->repeater_field . '_' . $key . '_slides_' . $i . '_slide_content', false );
					$slide_content[] = sprintf( '<div class="carousel-item %1$s">%2$s</div>', $active, apply_filters( 'the_content', html_entity_decode( $content ) ) );
				} else {
					$post_id      = $this->get_field( $this->repeater_field . '_' . $key . '_slides_' . $i . '_posts', false );
					$post         = get_post( $post_id );
					$post_content = apply_filters( 'the_content', html_entity_decode( $post->post_content ) );
					$post_image   = get_the_post_thumbnail( $post_id, 'post-thumbnail', array( 'class' => 'object-fit-cover' ) );
					if ( get_post_type( $post_id ) === 'cpt-testimonials' ) {
						$provided_by  = get_post_meta( $post_id, 'testimonial_provided_by', true );
						$job_title    = get_post_meta( $post_id, 'testimonial_job_title', true );
						$dealership   = get_post_meta( $post_id, 'testimonial_dealership', true );
						$image_str    = ( $post_image ? '<span class="bf-left">' . $post_image . '</span>' : '' );
						$citation     = ( $job_title || $dealership ) ? sprintf( '<cite title="Source Title">%1$s %2$s</cite>', $job_title, ( $dealership ? ' at ' . $dealership : '' ) ) : '';
						$slide_content[] = sprintf( '<div class="carousel-item %1$s"><blockquote class="blockquote">%2$s<footer class="blockquote-footer">%3$s<span class="bf-right">%4$s %5$s</span></footer></blockquote></div>', $active, $post_content, $image_str, $provided_by, $citation );
					} else {
						$slide_content[] = sprintf( '<div class="carousel-item %1$s">%2$s</div>', $active, $post_content );
					}
				}


				$slide_indicators[] = sprintf( '<li data-target="#carousel_%1$s" data-slide-to="%2$d" class="carousel-indicator %3$s"></li>', $rand_str, $i, $active );

			}

			ob_start();

			?>
			<section class="section-slider-testimonial <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> <?php echo ( esc_html( $css_class ) ); ?>">
				<a class="anchor" name="anchor-<?php echo $section_id; ?>"></a>
				<div class="<?php echo esc_attr( $this->container ); ?>">
					<?php if ( $section_title ) { ?>
						<div class="row justify-content-center">
							<div class="col-12 col-md-10 col-xl-8 text-center">
								<h2 class="section-title"><?php echo $section_title; ?></h2>
							</div>
						</div>
					<?php } ?>
					<div class="row justify-content-center">
						<div class="col-12 col-md-10 col-lg-9">
							<div id="carousel_<?php echo $rand_str; ?>" class="carousel carousel_<?php echo $rand_str; ?>" data-ride="false" data-interval="<?php echo ( $interval ? $interval : false ); ?>">
								<div class="carousel-inner text-center"><?php echo implode( '', $slide_content ); ?></div>
								<?php if ( $show_indicators ) { ?>
									<ol class="carousel-indicators">
										<?php echo implode( '', $slide_indicators ); ?>
									</ol>
								<?php } ?>
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

		$section_id      = $this->get_field( $this->repeater_field . '_' . $key . '_section_id' );
		$section_color = $this->get_field( $this->repeater_field . '_' . $key . '_section_color', false );
		$line_color    = $this->get_field( $this->repeater_field . '_' . $key . '_line_color', false );

		ob_start();

		?>
		<section class="section-row-break <?php echo ( $section_color ? 'bg-' . $section_color : '' ); ?> py-0">
			<a class="anchor" name="anchor-<?php echo $section_id; ?>"></a>
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
