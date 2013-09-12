<?php
/**
 * @package WordPress
 * @subpackage Custom_Taxonomies
 * @since Ben Marshall 1.0
 */

function customtaxonomies_init() {
	global $custom_taxonomies;
	foreach ( $custom_taxonomies as $key => $ary ) {
		$labels = array(
			'name' => _x( $ary['name'], 'taxonomy general name' ),
			'singular_name' => _x( $ary['singular_name'], 'taxonomy singular name' ),
			'search_items' => __( 'Search ' . $ary['name'] ),
			'all_items' => __( 'All ' . $ary['name'] ),
			'parent_item' => __( 'Parent ' . $ary['singular_name'] ),
			'parent_item_colon' => __( 'Parent ' . $ary['singular_name'] . ':' ),
			'edit_item' => __( 'Edit ' . $ary['singular_name'] ), 
			'update_item' => __( 'Update ' . $ary['singular_name'] ),
			'add_new_item' => __( 'Add New ' . $ary['singular_name'] ),
			'new_item_name' => __( 'New ' . $ary['singular_name'] ),
			'menu_name' => __( $ary['name'] )
		);
		$args = array(
			'labels' => $labels,
			'hierarchical' => $ary['type'] == 'tag' ? false : true,
		);
		register_taxonomy( $key, $ary['post_type'], $args );
	}
}

add_action( 'init', 'customtaxonomies_init', 0 );