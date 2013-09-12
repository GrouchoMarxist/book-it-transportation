<?php
/**
 * @package Ben Marshall
 * @subpackage Custom_Posts
 * @since Ben Marshall 1.0
 */

function customposts_init() {
	global $custom_posts;
	foreach ( $custom_posts as $key => $ary ) {
		$labels = array(
			'name' => _x( $ary['name'], 'post type general name' ),
			'singular_name' => _x( $ary['singular_name'], 'post type singular name' ),
			'add_new' => _x( 'Add New' ),
			'add_new_item' => __( 'Add New ' . $ary['singular_name'] ),
			'edit_item' => __( 'Edit ' . $ary['singular_name'] ),
			'new_item' => __( 'New ' . $ary['singular_name'] ),
			'all_items' => __( 'All ' . $ary['name'] ),
			'view_item' => __( 'View ' . $ary['singular_name'] ),
			'search_items' => __( 'Search ' . $ary['name'] ),
			'not_found'	=> __( 'No ' . strtolower($ary['name']) . ' found' ),
			'not_found_in_trash' => __( 'No ' . strtolower($ary['name']) . ' found in the Trash' ), 
			'parent_item_colon' => '',
			'menu_name' => $ary['name']
		);
		$args = array(
			'labels' => $labels,
			'public' => $ary['show_admin_ui'],
			'menu_position' => 5,
			'supports' => $ary['supports'],
			'has_archive' => $ary['enable_archives']
		);
		register_post_type( $key, $args );	
	}
}

add_action( 'init', 'customposts_init' );