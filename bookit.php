<?php
/*
Plugin Name: Book It! Transportation
Plugin URI: http://www.benmarshall.me/book-it-transportation/
Description: A complete management system for your transportation business enabling you to easily accept and manage your transportation bookings.
Version: 2.0
Author: Ben Marshall
Author URI: http://www.benmarshall.me
*/

/**
 * @package Book_It
 * @version 2.0
 */

// Setup Custom Posts
$custom_posts = array();

$custom_posts['bookit_reservation'] = array(
	'name' => 'Reservations',
	'singular_name' => 'Reservation',
	'show_admin_ui' => true,
	'supports' => array( 'title', 'excerpt', 'comments' ),
	'enable_archives' => false
);

// Setup Custom Taxonomies
$custom_taxonomies = array();

$custom_taxonomies['bookit_event_type'] = array(
	'name' => 'Event Types',
	'singular_name' => 'Event Type',
	'type' => 'tag',
	'post_type' => 'bookit_reservation'
);

$custom_taxonomies['bookit_outsource_company'] = array(
	'name' => 'Outsource Companies',
	'singular_name' => 'Outsource Company',
	'type' => 'tag',
	'post_type' => 'bookit_reservation'
);

$custom_taxonomies['bookit_vehicle'] = array(
	'name' => 'Vehicles',
	'singular_name' => 'Vehicle',
	'type' => 'tag',
	'post_type' => 'bookit_reservation'
);

// Setup Custom Meta Boxes

// Reservation Details Box
add_action( 'add_meta_boxes', 'bookit_reservation_details' );
function bookit_reservation_details() {
	add_meta_box( 'reservation_details', __( 'Reservation Details', 'bookit' ), 'bookit_reservation_details_box', 'bookit_reservation', 'normal', 'high' );
}
function bookit_reservation_details_box( $post ) {
	wp_nonce_field( 'bookit_reservation_details_box', 'reservation_details_nonce' );
	require_once( plugin_dir_path(__FILE__) . '/tpl/reservation_details_box.tpl.php' );
}

// Reservation Notifications Box
add_action( 'add_meta_boxes', 'bookit_notifications_box' );
function bookit_notifications_box() {
	add_meta_box( 'notifications_box', __( 'Notification Options', 'bookit' ), 'bookit_reservation_notifications_box', 'bookit_reservation', 'side', 'high' );
}
function bookit_reservation_notifications_box( $post ) {
	require_once( plugin_dir_path(__FILE__) . '/tpl/notifications_box.tpl.php' );
}

// On Save
add_action( 'save_post', 'bookit_reservation_details_box_save' );
function bookit_reservation_details_box_save( $post_id ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

	if ( !wp_verify_nonce( $_POST['reservation_details_nonce'], 'bookit_reservation_details_box' ) ) return;

	if ( 'bookit_reservation' == $_POST['post_type'] ) {
		if ( !current_user_can( 'edit_post', $post_id ) ) return;
	}

	foreach ( $_POST['bookit_destination'] as $key => $val ) {
		if(!$val) {
			unset($_POST['bookit_destination'][$key]);
		}
	}
	$_POST['bookit_destination'] = array_values($_POST['bookit_destination']);

	update_post_meta( $post_id, 'bookit_reservation_date', $_POST['bookit_reservation_date'] );
	update_post_meta( $post_id, 'bookit_pickup_time', $_POST['bookit_pickup_time'] );
	update_post_meta( $post_id, 'bookit_contact_name', $_POST['bookit_contact_name'] );
	update_post_meta( $post_id, 'bookit_contact_phone', $_POST['bookit_contact_phone'] );
	update_post_meta( $post_id, 'bookit_reservation_hours', $_POST['bookit_reservation_hours'] );
	update_post_meta( $post_id, 'bookit_num_passengers', $_POST['bookit_num_passengers'] );
	update_post_meta( $post_id, 'bookit_pickup_location', $_POST['bookit_pickup_location'] );
	update_post_meta( $post_id, 'bookit_destination', json_encode($_POST['bookit_destination']) );
}

// DO NOT EDIT BELOW THIS LINE!

add_action( 'save_post', 'bookit_set_title' );
function bookit_set_title() {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
}

add_action( 'admin_enqueue_scripts', 'bookit_scripts' );
function bookit_scripts() {
	global $post_type;
  if( 'bookit_reservation' == $post_type ) {
  	wp_enqueue_style( 'fontawesome', '//netdna.bootstrapcdn.com/font-awesome/3.2.1/css/font-awesome.css' );
  	wp_enqueue_style( 'bookit', plugins_url('assets/css/styles.css', __FILE__));

  	wp_enqueue_script('timeago', plugins_url('assets/js/jquery.timeago.js', __FILE__), array('jquery'));
  }
}

add_filter( 'gettext', 'bookit_gettext', 10, 2 );
function bookit_gettext( $translation, $original ) {
	global $post_type;
	if( 'bookit_reservation' == $post_type ) {
		if ( 'Excerpt' == $original ) {
	  	return 'Client Notes';
		}else{
	    $pos = strpos($original, 'Excerpts are optional hand-crafted summaries of your');
	    if ($pos !== false) {
	      return  '';
	    }
		}
	}
	return $translation;
}

add_filter( 'enter_title_here', 'change_enter_title_text', 10, 2 );
function change_enter_title_text( $text, $post ) {
	global $post_type;
	if( 'bookit_reservation' == $post_type ) {
		return 'Reservation Confirmation Code (leave blank to auto-generate)';
	}
}

require_once( plugin_dir_path(__FILE__) . '/custom-posts/functions.php' );
require_once( plugin_dir_path(__FILE__) . '/custom-taxonomies/functions.php' );