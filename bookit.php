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

// Save Box
add_action( 'add_meta_boxes', 'bookit_save_box' );
function bookit_save_box() {
	$plugin = get_plugin_data( __FILE__ );
	add_meta_box( 'save_box', __( 'Powered by Book It! v' . $plugin['Version'], 'bookit' ), 'bookit_reservation_save_box', 'bookit_reservation', 'side', 'high' );
}
function bookit_reservation_save_box( $post ) {
	require_once( plugin_dir_path(__FILE__) . '/tpl/save_box.tpl.php' );
}

// Advertisement Box
add_action( 'add_meta_boxes', 'bookit_adver_box' );
function bookit_adver_box() {
	add_meta_box( 'adver_box', __( 'Advertisement', 'bookit' ), 'bookit_reservation_adver_box', 'bookit_reservation', 'side', 'high' );
}
function bookit_reservation_adver_box( $post ) {
	require_once( plugin_dir_path(__FILE__) . '/tpl/adver_box.tpl.php' );
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

add_filter( 'enter_title_here', 'bookit_change_enter_title_text', 10, 2 );
function bookit_change_enter_title_text( $text, $post ) {
	global $post_type;
	if( 'bookit_reservation' == $post_type ) {
		return 'Reservation Confirmation Code (leave blank to auto-generate)';
	}
}

require_once( plugin_dir_path(__FILE__) . '/custom-posts/functions.php' );
require_once( plugin_dir_path(__FILE__) . '/custom-taxonomies/functions.php' );

function bookit_render_statuses() {
	$options = bookit_get_options();
	?>
	jhkhjkh
	<?php
}

function bookit_render_code_length() {
	$options = bookit_get_options();
	?>
	<p><label for="code-length">
			<input type="number" min="5" name="bookit_code_length" id="code-length" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_code_length', true)) ?>" class="small-text" >
		</label></p>
	<?php
}

add_action( 'init', 'bookit_init' );
function bookit_init() {
	bookit_listen();
}

function bookit_listen() {
	if( $_POST ) {
		if( isset( $_POST['bookit_action'] ) ) {

		}
	}
}

add_action( 'admin_init', 'bookit_options_init' );
function bookit_options_init() {
	register_setting( 'bookit_options', 'bookit_plugin_options', 'bookit_plugin_options_validate' );

	add_settings_section( 'bookit_options', 'General Settings', '__return_false', 'bookit_settings' );

	add_settings_field( 'bookit_statuses', __( 'Reservation Status Options', 'bookit' ), 'bookit_render_statuses', 'bookit_settings', 'bookit_options' );
	add_settings_field( 'bookit_code_length', __( 'Confirmation Code Length', 'bookit' ), 'bookit_render_code_length', 'bookit_settings', 'bookit_options' );
}


add_action( 'admin_menu', 'bookit_plugin_options_add_page' );
function bookit_plugin_options_add_page() {
	add_plugins_page( __( 'Book It! Options', 'bookit' ), __( 'Book It! Options', 'bookit' ), 'edit_theme_options', 'bookit_options', 'bookit_plugin_options_render_page' );

  remove_meta_box('tagsdiv-bookit_event_type', 'bookit_reservation', 'side');
  remove_meta_box('tagsdiv-bookit_outsource_company', 'bookit_reservation', 'side');
  remove_meta_box('tagsdiv-bookit_vehicle', 'bookit_reservation', 'side');
  remove_meta_box('submitdiv', 'bookit_reservation', 'side');
  remove_meta_box('commentstatusdiv', 'bookit_reservation', 'normal');

}

function bookit_get_options() {
	$saved = (array) get_option( 'bookit_plugin_options' );
	$defaults = array();

	$defaults = apply_filters( 'bookit_default_theme_options', $defaults );

	$options = wp_parse_args( $saved, $defaults );
	$options = array_intersect_key( $options, $defaults );

	return $options;
}

function bookit_plugin_options_render_page() {
	?>
	<div class="wrap">
		<?php screen_icon(); ?>
		<h2><?php echo __('Book It! Transportation Options', 'bookit'); ?></h2>
		<?php settings_errors(); ?>

		<form method="post" action="options.php">
			<?php
				settings_fields( 'bookit_options' );
				do_settings_sections( 'bookit_settings' );
				submit_button();
			?>
		</form>
	</div>
	<?php
}

function bookit_plugin_options_validate( $input ) {
	$output = array();

	return apply_filters( 'bookit_plugin_options_validate', $output, $input );
}

function bookit_randString($length=10, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
	if(!$length) $length = 10;
  $str = '';
  $count = strlen($charset);
  while ($length--) {
    $str .= $charset[mt_rand(0, $count-1)];
  }
  return $str;
}

add_filter( 'wp_insert_post_data' , 'bookit_auto_generate_title' , '99', 2 );
function bookit_auto_generate_title( $data , $postarr ) {
	global $wpdb;
	if ( $data['post_type'] == 'bookit_reservation' && strlen($data['post_title']) < 1 ) {
		$unique = false;
		while(!$unique) {
			$rand = bookit_randString(get_post_meta($post->ID, 'bookit_code_length', true));
			$query = $wpdb->prepare('SELECT ID FROM ' . $wpdb->posts . ' WHERE post_title = %s AND post_type = \'bookit_reservation\'', $rand);
			$wpdb->query( $query );
			if ( !$wpdb->num_rows ) {
				$unique = true;
			}
		}
		$data['post_title'] = $rand;
	}
	return $data;
}

function bookit_force_type_private ( $post ) {
	if ($post['post_type'] == 'bookit_reservation' && $post['post_status'] == 'publish') $post['post_status'] = 'private';
	
	return $post;
}
add_filter('wp_insert_post_data', 'bookit_force_type_private');