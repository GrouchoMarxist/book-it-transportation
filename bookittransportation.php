<?php
/*
Plugin Name: Book It! Transportation
Plugin URI: http://www.benmarshall.me/book-it-transportation/
Description: A complete management system for your transportation business enabling you to easily accept and manage your transportation bookings.
Version: 1.0.4
Author: Ben Marshall
Author URI: http://www.benmarshall.me
*/
include( plugin_dir_path( __FILE__ ) . 'config.php' );

function bookit_add_settings() {
  register_setting( 'bookit_options', 'bookit_reservation_received_url', 'bookit_isValidURL' );
  register_setting( 'bookit_options', 'bookit_default_reservation_status' );
  register_setting( 'bookit_options', 'bookit_reservation_failed_url', 'bookit_isValidURL' );
  register_setting( 'bookit_options', 'bookit_emails_default_subject', 'bookit_emailSubject' );
  register_setting( 'bookit_options', 'bookit_emails_new_reservation_subject', 'bookit_emailSubject' );
  register_setting( 'bookit_options', 'bookit_emails_new_reservation_template' );
  register_setting( 'bookit_options', 'bookit_emails_reservation_confirmed_subject', 'bookit_emailSubject' );
  register_setting( 'bookit_options', 'bookit_emails_reservation_confirmed_template' );
  register_setting( 'bookit_options', 'bookit_emails_outsource_subject', 'bookit_emailSubject' );
  register_setting( 'bookit_options', 'bookit_emails_outsource_template' );
}

add_action( 'admin_init', 'bookit_admin' );
function bookit_admin() {
  global $pagenow, $bookit_config;
  add_meta_box( 'reservation_details', 'Reservation Details', 'display_resevation_details', 'bookit_reservation', 'normal', 'core' );
  bookit_add_settings();
}

add_action( 'init', 'bookit_init' );
function bookit_init() {
  global $bookit_config;
  if (!session_id()) {
    session_start();
  }
  // Required
  if( ! get_option( 'bookit_default_reservation_status' ) ) {
     update_option( 'bookit_default_reservation_status', 'pending-review' );
  }
  if( ! get_option( 'bookit_reservation_received_url' ) ) {
     update_option( 'bookit_reservation_received_url', get_site_url() );
  }
  if( ! get_option( 'bookit_reservation_failed_url' ) ) {
     update_option( 'bookit_reservation_failed_url', get_site_url() );
  }

  bookit_add_post_types();
  bookit_add_categories();
  bookit_process_post();
}

function bookit_add_post_types() {
  global $bookit_config;
  foreach( $bookit_config['post_types'] as $key => $value ) {
    register_post_type( $key , $value['args'] );
  }
}

function bookit_add_categories() {
  global $bookit_config;
  foreach( $bookit_config['categories'] as $key => $value ) {
    register_taxonomy( $key, $value['post_types'], array(
      'hierarchical'      => true,
      'labels'            => $value['labels'],
      'show_ui'           => true,
      'show_admin_column' => true,
      'query_var'         => false,
      'rewrite'           => false
    ) );
  }
}

function bookit_process_post() {
  if( $_POST ) {
    if( isset( $_POST['bookit_action'] ) ) {
      switch( $_POST['bookit_action'] ) {
        case 'send_new_reservation_email':
          if( isset( $_POST['ID'] ) ) {
            $result = bookit_send_email( $_POST['ID'], 'new_reservation' );
            if( $result == 'success' ) {
              echo __( 'Email successfully sent.', 'bookit' );
            } else {
              if ( is_array($result) ) {
                foreach( $result as $key => $value ) {
                  echo __( '<strong>Failed:</strong> ' . $value, 'bookit' );
                }
              }
            }
          }
          die();
          break;
       case 'send_reservation_confirmed':
          if( isset( $_POST['ID'] ) ) {
            $result = bookit_send_email( $_POST['ID'], 'reservation_confirmed' );
            if( $result == 'success' ) {
              echo __( 'Email successfully sent.', 'bookit' );
            } else {
              if ( is_array($result) ) {
                foreach( $result as $key => $value ) {
                  echo __( '<strong>Failed:</strong> ' . $value, 'bookit' );
                }
              }
            }
          }
          die();
          break;
        case 'send_reservation_outsource':
          if( isset( $_POST['ID'] ) ) {
            $result = bookit_send_email( $_POST['ID'], 'outsource' );
            if( $result == 'success' ) {
              echo __( 'Email successfully sent.', 'bookit' );
            } else {
              if ( is_array($result) ) {
                foreach( $result as $key => $value ) {
                  echo __( '<strong>Failed:</strong> ' . $value, 'bookit' );
                }
              }
            }
          }
          die();
          break;
        case 'bookit-reservation':
          bookit_add_reservation();
          break;
      }
    }
  }
}
function bookit_add_reservation() {
  global $bookit_config;
  $page = $_POST['page'];
  $errors = array();
  foreach( $bookit_config['required_fields'] as $key => $value ) {
    if( ! isset( $_POST[$value] ) || isset( $_POST[$value] ) && ! $_POST[ $value ]) {
      $errors[] = $value;
    }
  }
  if( count( $errors ) > 0 ) {
    $_SESSION['bookit']['post']   = $_POST;
    $_SESSION['bookit']['errors'] = $errors;
    wp_redirect( $page );
    exit;
  } else {
    $post = array(
      'comment_status' => 'closed',
      'ping_status'    => 'closed',
      'post_author'    => get_current_user_id(),
      'post_status'    => $bookit_config['reservation-status'],
      'post_title'     => bookit_randString(),
      'post_type'      => 'bookit_reservation'
    );

    $post_id = wp_insert_post( $post );
    if( $post_id ) {
      wp_set_object_terms( $post_id, $_POST['vehicle'], 'vehicle', true );
      wp_set_object_terms( $post_id, $_POST['destinations'], 'destinations', true );
      wp_set_object_terms( $post_id, $_POST['pickup'], 'pickup', true );
      wp_set_object_terms( $post_id, $_POST['event_type'], 'event_type', true );
      if ( bookit_send_email( $post_id, 'new_reservation' ) == 'success' ) {
        wp_redirect( $bookit_config['reservation-received-url'] );
      } else {
        wp_redirect( $bookit_config['reservation-failed-url'] );
      }
      exit;
    }
  }
}

// Functionality for sending emails
// $type, string - Available options new_reservation
function bookit_send_email( $ID, $type ) {
  global $bookit_config;
  $errors = array();
  $post   = get_post( $ID );
  if ( $post ) {
    $meta   = get_post_custom( $post->ID );
    if ( isset($meta['contact_email'][0]) && is_email( $meta['contact_email'][0] ) ) {
      $contact_name  = isset($meta['contact_name'][0]) ? $meta['contact_name'][0] : '';
      $user_email = $meta['contact_email'][0];
      $to = $contact_name . '<' . $user_email . '>';
      $email  = isset($bookit_config['emails'][$type]) ? $bookit_config['emails'][$type] : false;
      if ( $email ) {
        $subject = isset($email['subject']) ? $email['subject'] : get_option('bookit_emails_default_subject');
        $template = isset($email['template']) ? $email['template'] : false;
        if ( $template ) {
          $categories = array();
          foreach( $bookit_config['categories'] as $key => $value ) {
            $category = wp_get_post_terms( $post->ID, $key );
            $categories[$key] = $category;
          }
          $ary = array(
            'title' => $post->post_title
          );
          foreach( $bookit_config['fields'] as $key => $array ) {
            $ary[$array['key']] = $meta[$array['key']][0];
          }
          foreach( $categories as $tag => $array ) {
            foreach( $array as $k => $v ) {
              if( isset( $ary[$tag] ))  $ary[$tag] .= ', ';
              $ary[$tag] .= $v->name;
            }
          }
          $headers[] = 'From: ' . get_bloginfo( 'admin_name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>';
          $headers[] = 'Bcc: ' . get_bloginfo( 'admin_name' ) . ' <' . get_bloginfo( 'admin_email' ) . '>';
          add_filter( 'wp_mail_content_type', create_function( '', 'return "text/html";' ) );
          if ( ! wp_mail( $to, bookit_tags( $subject ), bookit_tags( $template, $ary ), $headers ) ) {
            $errors[] = __( 'There was a problem sending the email.', 'bookit' );
          }
        } else {
          $errors[] = __( 'You must first create a <a href="wp-admin/options-general.php?page=bookit">email template</a>.', 'bookit' );
        }
      } else {
        $errors[] = __( 'Unable to locate the \'' . $type . '\' email type.', 'bookit' );
      }
    } else {
      $errors[] = __( 'The user\'s email is invaild.', 'bookit' );
    }
  } else {
    $errors[] = __( 'Unable to load the post.', 'bookit' );
  }

  if (count($errors) > 0 ) {
    return $errors;
  } else {
    return 'success';
  }
}
// Rewrites email template tags
function bookit_tags($html,$ary) {
  $find = array();
  $replace = array();
  foreach($ary as $key=>$value) {
    $find[] = '[['.strtoupper($key).']]';
    $replace[] = $value;
  }
  if(isset($ary['month']) && isset($ary['date']) && isset($ary['year'])) {
    $find[] = '[[RESERVATION_DATE_FULLTEXT]]';
    $replace[] = date('l, F jS, Y g:ia',strtotime($ary['month'].'-'.$ary['date'].'-'.$ary['year'].' '.$ary['time']));
  }
  $html = str_replace( $find, $replace, $html );
  return $html;
}

function bookit_emailSubject($value) {
  if(strlen($value) > 80) {
    add_settings_error(
      'bookit_confirmation_email_subject',
      'bookit_confirmation_email_subject_error',
      'To help avoid spam filters, avoid a email subject with more than 80 characters.',
      'error'
    );
  }
  return $value;
}
function bookit_isValidURL($value) {
  $response = wp_remote_get( esc_url_raw( $value ) );
  if (is_wp_error( $response ) ) {
    add_settings_error(
      'bookit_reservation_received_url',
      'bookit_reservation_received_url_error',
      '"<em>' . $value . '</em>" is not a valid URL.',
      'error'
    );
  }
  return $value;
}

function display_resevation_details($object) {
  global $bookit_config;
  ?>
  <?php
  wp_nonce_field( basename( __FILE__ ), 'bookit_nonce' );
  $outsource = get_the_terms( $ID, 'outsource_companies', '', '', '' );
  $term_args=array(
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
  );
  $terms = get_terms('outsource_companies',$term_args);
  include( plugin_dir_path( __FILE__ ) . 'inc/reservation-details.php' );
}
function display_notification_options() {
  include( plugin_dir_path( __FILE__ ) . 'inc/notification-options.php' );
}
add_action( 'save_post', 'bookit_save_reservation', 10, 2 );
function bookit_save_reservation($post_id, $reservation_details) {
  global $bookit_config;
  if ( $reservation_details->post_type == 'bookit_reservation' ) {
    foreach($bookit_config['fields'] as $key=>$value) {

      $field = ( isset( $_POST[$value['key']] ) ? $_POST[$value['key']] : $value['default'] );
      $current_field = get_post_meta( $reservation_details->ID, $value['key'], true );
      if ( $field && '' == $current_field ) {
        add_post_meta( $reservation_details->ID, $value['key'], $field, true );
      } elseif ( $field && $field != $current_field ) {
        update_post_meta( $reservation_details->ID, $value['key'], $field );
      } elseif ( '' == $field && $current_field ) {
        delete_post_meta( $reservation_details->ID, $value['key'], $field );
      }
    }
  }
}

// Add filters
add_filter( 'enter_title_here', 'bookit_change_enter_title_text', 10, 2 );
function bookit_change_enter_title_text( $text, $post ) {
  if( $post->post_type == 'bookit_reservation') {
    return __( 'Enter the reservation confirmation code', 'bookit');
  } else {
    return $text;
  }
}
add_filter( 'gettext', 'change_publish_button', 10, 2 );
function change_publish_button( $translation, $text ) {
  if( 'bookit_reservation' == get_post_type())
    if ( $text == 'Publish' ) {
      return 'Save Reservation';
    }
  return $translation;
}
// Add reservation form shortcode
add_shortcode( 'bookit_reservation_form', 'bookit_shortcode_reservation_form' );
function bookit_shortcode_reservation_form( $atts ) {
  global $bookit_config, $post;
  $current_user = wp_get_current_user();
  extract( shortcode_atts( array(
  ), $atts ) );
  $html = '<form id="bookit-reservation" name="bookit-reservation" method="post" action="'.get_permalink().'"><input type="hidden" name="bookit_action" value="bookit-reservation">'.wp_nonce_field( 'bookit_nonce' );
  $events = get_terms( 'event_type', array(
    'orderby'    => 'count',
    'hide_empty' => 0
  ) );
  $vehicles = get_terms( 'vehicle', array(
    'orderby'    => 'count',
    'hide_empty' => 0
  ) );
  ?>
  <? if(isset($_SESSION['bookit']['errors'])): ?>
  <div class="message-box-wrapper red">
    <div class="message-box-title"><?=__('Sorry, there was a problem processing your reservation, see below.') ?></div>
    <div class="message-box-content"><ul><?
    foreach($_SESSION['bookit']['errors'] as $key=>$value):
      if($value == 'contact_name'):
        echo '<li>'.__('Please enter your <strong>name</strong>.');
      elseif($value == 'contact_phone'):
        echo '<li>'.__('Please enter your <strong>phone number</strong>.');
      elseif($value == 'contact_email'):
        echo '<li>'.__('Please enter your <strong>email address</strong>.');
      elseif($value == 'num_passengers'):
        echo '<li>'.__('Please enter the <strong>number of passengers</strong>.');
      elseif($value == 'vehicle'):
        echo '<li>'.__('Please select your <strong>vehicle</strong> preference.');
      elseif($value == 'event_type'):
        echo '<li>'.__('Please select the <strong>event type</strong>.');
      endif;
    endforeach;
    ?></ul></div>
  </div>
  <?  unset($_SESSION['bookit']['errors']); endif; ?>
  <?
  if(isset($_SESSION['bookit']['post'])) $bookit_config['post'] = $_SESSION['bookit']['post'];
  foreach($bookit_config['fields'] as $key=>$value) {
    ob_start();
    ?>
    <input type="hidden" name="page" value="<?=get_permalink() ?>">
    <div class="bookit-field <?=$value['key'] ?>">
      <div class="bookit-label"><label for="<?=$value['key'] ?>"><?=$value['name'] ?></label></div>
      <div class="bookit-input">
        <?
        if($value['type'] == 'text' || $value['type'] == 'number'  || $value['type'] == 'tel' || $value['type'] == 'email'): ?>
          <input type="<?=$value['type'] ?>" name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" value="<?=stripslashes($bookit_config['post'][$value['key']]) ?>" placeholder="<?=$value['placeholder'] ?>" <? if($value['type'] == 'number'): ?>min="0"<? endif; ?>>
        <? elseif($value['type'] == 'textarea'): ?>
          <textarea name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" cols="50" rows="5"><?=stripslashes($bookit_config['post'][$value['key']]) ?></textarea>
        <? elseif($value['type'] == 'select'): ?>
          <select name="<?=$value['key'] ?>" id="<?=$value['key'] ?>">
            <? foreach($value['options'] as $k=>$v): ?>
            <option value="<?=$k ?>" <? if(stripslashes($bookit_config['post'][$value['key']]) == $k): ?>selected="selected"<? endif; ?>><?=$v ?></option>
            <? endforeach; ?>
          </select>
        <? endif; ?>
      </div>
    </div>
    <?
    $html .= ob_get_contents();
    ob_end_clean();
  }
  ob_start();

  ?>
  <div class="bookit-field pickup">
    <div class="bookit-label"><label for="pickup"><?=_e('Where would you like to be picked up at?','bookit') ?></label></div>
    <div class="bookit-input">
      <input type="text" name="pickup[]" id="pickup" value="<?=stripslashes($bookit_config['post']['pickup'][0]) ?>" class="bookit-pickup" placeholder="<?=_e('Enter a address, business name or landmark','bookit') ?>">
    </div>
  </div>
  <div class="bookit-field destinations">
    <div class="bookit-label"><label for="destinations"><?=_e('Tell us where you\'d like to be taken:','bookit') ?></label></div>
    <div class="bookit-input" id="cell-destinations">
      <input type="text" name="destinations[]" class="bookit-destinations" value="<?=stripslashes($bookit_config['post']['destinations'][0]) ?>" placeholder="<?=_e('Enter a address, business name or landmark','bookit') ?>"> <a href="#" id="bookit_add">Add another destination &raquo;</a>
    </div>
    <script>
    jQuery(function() {
      jQuery('#bookit_add').live('click',function(e) {
        e.preventDefault();
        jQuery('#bookit_add').before(jQuery("#cell-destinations input").first().clone());
      });
    });
    </script>
  </div>
  <div class="bookit-field vehicle">
    <div class="bookit-label"><label for="vehicle"><?=_e('Select your perferred vehicle:','bookit') ?></label></div>
    <div class="bookit-input">
      <select name="vehicle" id="vehicle">
        <? foreach($vehicles as $key=>$value): ?>
        <option value="<?=$value->slug ?>" <? if(stripslashes($bookit_config['post']['vehicle']) == $value->slug): ?>selected="selected"<? endif; ?>><?=$value->name ?></option>
        <? endforeach; ?>
      </select>
    </div>
  </div>
  <div class="bookit-field event-type">
    <div class="bookit-label"><label for="event-type"><?=_e('Select a service type:','bookit') ?></label></div>
    <div class="bookit-input">
      <select name="event_type" id="event_type">
        <? foreach($events as $key=>$value): ?>
        <option value="<?=$value->slug ?>" <? if(stripslashes($bookit_config['post']['event_type']) == $value->slug): ?>selected="selected"<? endif; ?>><?=$value->name ?></option>
        <? endforeach; ?>
      </select>
    </div>
  </div>
  <?
  $html .= ob_get_contents();
  ob_end_clean();


  $html .= '<input type="submit" value="Submit Reservation" id="submit" name="submit"></form>';
  unset($_SESSION['bookit']['post']);
  return $html;
}
add_action('wp_enqueue_scripts', 'bookit_add_scipts');
function bookit_add_scipts() {
  wp_enqueue_script(
    'jquery_ui',
    plugins_url('/assets/js/jquery-ui-1.10.0.custom.min.js', __FILE__),
    array('jquery')
  );
  wp_enqueue_script(
    'bookit',
    plugins_url('/assets/js/script.js', __FILE__),
    array('jquery_ui')
  );
  wp_register_style( 'jquery_ui_benmarshall', plugins_url('/assets/css/benmarshall/jquery-ui-1.10.0.custom.min.css', __FILE__) );
  wp_enqueue_style( 'jquery_ui_benmarshall' );

  $categories = get_terms( 'destinations', array(
    'orderby'    => 'count',
    'hide_empty' => 0
   ) );
  wp_localize_script( 'bookit', 'bookit', $categories );
}

add_action('wp_logout', 'bookit_end_session');
add_action('wp_login', 'bookit_end_session');
function bookit_end_session() {
  unset($_SESSION['bookit']);
}

function bookit_randString($length=10, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
  $str = '';
  $count = strlen($charset);
  while ($length--) {
    $str .= $charset[mt_rand(0, $count-1)];
  }
  return $str;
}


add_action( 'admin_menu', 'bookit_admin_menu' );
function bookit_admin_menu() {
  add_options_page( 'Book It! Transportation Settings', 'Book It! Transportation', 'manage_options', 'bookit', 'bookit_options' );
  remove_meta_box('outsource_companiesdiv', 'bookit_reservation', 'side');
}
function bookit_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  include( plugin_dir_path( __FILE__ ) . 'inc/options.php');
}

function get_taxonomy_term_type($taxonomy,$term_id) {
  return get_option("_term_type_{$taxonomy}_{$term->term_id}");
}
function update_taxonomy_term_type($taxonomy,$term_id,$value) {
  update_option("_term_type_{$taxonomy}_{$term_id}",$value);
}

TaxonomyTermTypes::on_load();
TaxonomyTermTypes::register_taxonomy( array( 'outsource_companies' ) );
class TaxonomyTermTypes {
  static function on_load() {
    add_action( 'created_term', array( __CLASS__, 'term_type_update' ), 10, 3 );
    add_action( 'edit_term', array( __CLASS__, 'term_type_update' ), 10, 3 );
  }
  static function register_taxonomy($taxonomy) {
    if (!is_array($taxonomy))
      $taxonomy = array($taxonomy);
    foreach($taxonomy as $tax_name) {
      add_action("{$tax_name}_add_form_fields",array(__CLASS__,"add_form_fields"));
      add_action("{$tax_name}_edit_form_fields",array(__CLASS__,"edit_form_fields"),10,2);
    }
  }
  // This displays the selections. Edit it to retrieve
  static function add_form_fields($taxonomy) {
    echo __('Company Email', 'bookit') . self::get_select_html();
  }
  // This displays the selections. Edit it to retrieve your own terms however you retrieve them.
  static function get_select_html($selected='') {
    $html =<<<HTML
<input type="email" name="company_email" id="company_email" value="$selected">
HTML;
    return $html;
  }
    static function edit_form_fields($term, $taxonomy) {
    $selected = get_option("_term_type_{$taxonomy}_{$term->term_id}");
    $select = self::get_select_html($selected);
    $html =<<<HTML
      <tr class="form-field form-required">
        <th scope="row" valign="top"><label for="company_email">Company Email</label></th>
        <td>$select</td>
      </tr>
HTML;
    echo $html;
  }
  static function term_type_update($term_id, $tt_id, $taxonomy) {
    if ( isset( $_POST['company_email'] ) ) {
      update_taxonomy_term_type( $taxonomy, $term_id, $_POST['company_email'] );
    }
  }
}

add_filter( 'manage_edit-bookit_reservation_columns', 'add_new_bookit_reservation_columns' );
function add_new_bookit_reservation_columns( $bookit_reservation_columns ) {
  $bookit_reservation_columns['cb']             = '<input type="checkbox">';
  $bookit_reservation_columns['title']          = _x( 'Confirmation Code', 'column name');
  $bookit_reservation_columns['date_reserved']  = __( 'Date Reserved', 'bookit' );
  return $bookit_reservation_columns;
}

add_action( 'manage_bookit_reservation_posts_custom_column', 'manage_bookit_reservation_columns', 10, 2 );
function manage_bookit_reservation_columns( $column_name, $id ) {
  global $post;
  switch ( $column_name ) {
    case 'date_reserved':
      $month  = get_post_meta( $post->ID , 'month' , true );
      $date   = get_post_meta( $post->ID , 'date' , true );
      $year   = get_post_meta( $post->ID , 'year' , true );
      $time   = get_post_meta( $post->ID , 'time' , true );
      echo date( 'M. j, Y g:ia', strtotime($month . '-' . $date . '-' . $year . ' ' . $time) );
      break;
    default:
      break;
  }
}

add_filter( 'wp_insert_post_data' , 'bookit_dont_publish' , '99', 2 );
function bookit_dont_publish( $data , $postarr ) {
  if ($data['post_type'] == 'bookit_reservation' ){
    $data['post_status'] = 'draft';
  }
  return $data;
}

include( plugin_dir_path( __FILE__ ) . 'inc/premium.php' );