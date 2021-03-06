<?php
/*
  Plugin Name: Integrations Custom Post Type
  Description: This `mu-plugin` class creates a new custom post type for Integrations.
  Version: 1.0
  Author: Tim Spinks @monkishtypist
  Author URI: https://github.com/monkishtypist
*/

if ( ! class_exists( 'CPT_Integration_Partners' ) ) :

	class CPT_Integration_Partners {

		private $textdomain = '';

		public function __construct() {

			add_action( 'init', array( $this, 'integrations_post_type') );

		}

		/**
		 * Register "Integration Partners" post type
		 */
		public function integrations_post_type() {

			$labels = array(
				'name'                  => _x( 'Integration Partners', 'integrations_post_type', $this->textdomain ),
				'singular_name'         => _x( 'Integration Partner', 'integrations_post_type', $this->textdomain ),
				'menu_name'             => _x( 'Integration Partners', 'integrations_post_type', $this->textdomain ),
				'name_admin_bar'        => _x( 'Integration Partners', 'integrations_post_type', $this->textdomain ),
				'archives'              => __( 'Integration Partner Archives', $this->textdomain ),
				'parent_item_colon'     => __( 'Parent Item:', $this->textdomain ),
				'all_items'             => __( 'All Integration Partners', $this->textdomain ),
				'add_new_item'          => __( 'Add New Integration Partner', $this->textdomain ),
				'add_new'               => __( 'Add New', $this->textdomain ),
				'new_item'              => __( 'New Integration Partner', $this->textdomain ),
				'edit_item'             => __( 'Edit Integration Partner', $this->textdomain ),
				'update_item'           => __( 'Update Integration Partner', $this->textdomain ),
				'view_item'             => __( 'View Integration Partner', $this->textdomain ),
				'search_items'          => __( 'Search Integration Partners', $this->textdomain ),
				'not_found'             => __( 'Not found', $this->textdomain ),
				'not_found_in_trash'    => __( 'Not found in Trash', $this->textdomain ),
				'featured_image'        => __( 'Featured Image', $this->textdomain ),
				'set_featured_image'    => __( 'Set featured image', $this->textdomain ),
				'remove_featured_image' => __( 'Remove featured image', $this->textdomain ),
				'use_featured_image'    => __( 'Use as featured image', $this->textdomain ),
				'insert_into_item'      => __( 'Insert into item', $this->textdomain ),
				'uploaded_to_this_item' => __( 'Uploaded to this item', $this->textdomain ),
				'items_list'            => __( 'Items list', $this->textdomain ),
				'items_list_navigation' => __( 'Items list navigation', $this->textdomain ),
				'filter_items_list'     => __( 'Filter items list', $this->textdomain )
			);
			$rewrite = array(
				'slug'                  => 'integrations-partners',
				'with_front'            => false,
				'pages'                 => true,
				'feeds'                 => false
			);
			$args = array(
				'label'                 => __( 'Integration Partner', $this->textdomain ),
				'description'           => __( '', $this->textdomain ),
				'labels'                => $labels,
				'supports'              => array( 'title', 'editor', 'thumbnail' ),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 10,
				'menu_icon'             => 'dashicons-groups',
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,
				'exclude_from_search'   => true,
				'publicly_queryable'    => true,
				'rewrite'               => $rewrite,
				'capability_type'       => 'page',
				'show_admin_column'     => true
			);
			register_post_type( 'cpt-integrations', $args );
		}

	}

	$CPT_Integration_Partners = new CPT_Integration_Partners();

endif;
