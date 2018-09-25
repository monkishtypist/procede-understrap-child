<?php
function understrap_remove_scripts() {
    wp_dequeue_style( 'understrap-styles' );
    wp_deregister_style( 'understrap-styles' );

    wp_dequeue_script( 'understrap-scripts' );
    wp_deregister_script( 'understrap-scripts' );

    // Removes the parent themes stylesheet and scripts from inc/enqueue.php
}
add_action( 'wp_enqueue_scripts', 'understrap_remove_scripts', 20 );

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles' );
function theme_enqueue_styles() {

	// Get the theme data
	$the_theme = wp_get_theme();
    wp_enqueue_style( 'child-understrap-styles', get_stylesheet_directory_uri() . '/css/child-theme.min.css', array(), $the_theme->get( 'Version' ) );
    wp_enqueue_script( 'jquery');
	wp_enqueue_script( 'popper-scripts', get_template_directory_uri() . '/js/popper.min.js', array(), false);
    wp_enqueue_script( 'child-understrap-scripts', get_stylesheet_directory_uri() . '/js/child-theme.min.js', array(), $the_theme->get( 'Version' ), true );
    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}

function add_child_theme_textdomain() {
    load_child_theme_textdomain( 'understrap-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'add_child_theme_textdomain' );


/**
 * Initialize theme default settings
 */
// require get_stylesheet_directory() . '/inc/theme-settings.php';

/**
 * Theme setup and custom theme supports.
 */
// require get_stylesheet_directory() . '/inc/setup.php';

/**
 * Register widget area.
 */
require get_stylesheet_directory() . '/inc/widgets.php';

/**
 * Enqueue scripts and styles.
 */
// require get_stylesheet_directory() . '/inc/enqueue.php';

/**
 * Custom template tags for this theme.
 */
// require get_stylesheet_directory() . '/inc/template-tags.php';

/**
 * Custom pagination for this theme.
 */
// require get_stylesheet_directory() . '/inc/pagination.php';

/**
 * Custom hooks.
 */
require get_stylesheet_directory() . '/inc/hooks.php';

/**
 * Custom functions that act independently of the theme templates.
 */
// require get_stylesheet_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
// require get_stylesheet_directory() . '/inc/customizer.php';

/**
 * Custom Comments file.
 */
// require get_stylesheet_directory() . '/inc/custom-comments.php';

/**
 * Load Jetpack compatibility file.
 */
// require get_stylesheet_directory() . '/inc/jetpack.php';

/**
 * Load custom WordPress nav walker.
 */
// require get_stylesheet_directory() . '/inc/class-wp-bootstrap-navwalker.php';

/**
 * Load WooCommerce functions.
 */
// require get_stylesheet_directory() . '/inc/woocommerce.php';

/**
 * Load Editor functions.
 */
// require get_stylesheet_directory() . '/inc/editor.php';

/**
 * Load Advanced Custom Fields Partials.
 */
require get_stylesheet_directory() . '/inc/class-advanced-custom-fields-partials.php';
