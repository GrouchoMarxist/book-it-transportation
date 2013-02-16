<?php
/*
Plugin Name: Book It! Transportation
Plugin URI: http://www.benmarshall.me/book-it-transportation/
Description: A complete management system for your transportation business enabling you to easily accept and manage your transportation bookings
Version: 1.0.3
Author: Ben Marshall
Author URI: http://www.benmarshall.me
*/
include( plugin_dir_path( __FILE__ ) . 'config.php');

// Hooks
register_activation_hook( __FILE__, 'bookittrans_dependentplugin_check' );

// Runs after WordPress has finished loading but before any headers are sent. Useful for intercepting $_GET or $_POST triggers.
add_action( 'init', 'bookittrans_init' );
function bookittrans_init() {
  global $bookittrans_config;
  
  bookittrans_start_session();
  
  if(!get_option('bookittrans_default_reservation_status')) {
     update_option( 'bookittrans_default_reservation_status', 'pending-review' );
  }
  if(!get_option('bookittrans_confirmation_email_subject')) {
     update_option( 'bookittrans_confirmation_email_subject', 'Your reservation has beed confirmed' );
  }
  if(!get_option('bookittrans_reservation_email_subject')) {
     update_option( 'bookittrans_reservation_email_subject', 'We\'ve received your reservation request' );
  }
  
  // Check for pluing dependencies.
  bookittrans_dependentplugin_check();
  
  // Add custom post types.
  bookittrans_add_post_types();
  
  // Add custom post categories.
  bookittrans_add_categories();
  
  // Process POSTs
  bookittrans_process_post();
}
// Process POSTs
function bookittrans_process_post() {
  if($_POST) {
    if(isset($_POST['bookittrans_action'])) {
      switch($_POST['bookittrans_action']) {
        case 'send_reservation_received':
          if(isset($_POST['ID'])) {
            if(email_reservation_received($_POST['ID'])) {
              echo __('Email successfully sent.');
            } else {
              echo __('There was a problem sending the email.');
            }
          }
          die();
          break;
       case 'send_reservation_confirmed':
          if(isset($_POST['ID'])) {
            if(email_reservation_confirmed($_POST['ID'])) {
              echo __('Email successfully sent.');
            } else {
              echo __('There was a problem sending the email.');
            }
          }
          die();
          break;
        case 'send_reservation_outsource':
          if(isset($_POST['ID'])) {
            echo email_reservation_outsource($_POST['ID']);
          }
          die();
          break;
        case 'bookittrans-reservation':
          bookittrans_add_reservation();
          break;
      }
    }
  }
}
// Add a new resevation from the shortcode
function bookittrans_add_reservation() {
  global $bookittrans_config;
  $page = $_POST['page'];
  $errors = array();
  foreach($bookittrans_config['required_fields'] as $key=>$value) {
    if(!isset($_POST[$value]) || isset($_POST[$value]) && !$_POST[$value]) {
      $errors[] = $value;
    }
  }
  if(count($errors) > 0) {
    $_SESSION['bookittrans']['post'] = $_POST;
    $_SESSION['bookittrans']['errors'] = $errors;
    wp_redirect( $page );
    exit;
  } else {
    $post = array(
      'comment_status' => 'closed', // 'closed' means no comments.
      'ping_status'    => 'closed', // 'closed' means pingbacks or trackbacks turned off
      'post_author'    => get_current_user_id(), //The user ID number of the author.
      'post_status'    => $bookittrans_config['reservation-status'], //Set the status of the new post.
      'post_title'     => bookittrans_randString(), //The title of your post.
      'post_type'      => 'bookit_reservation'
    );
    
    $post_id = wp_insert_post( $post );
    if($post_id) {
      wp_set_object_terms($post_id, $_POST['vehicle'], 'vehicle', true);
      wp_set_object_terms($post_id, $_POST['destinations'], 'destinations', true);
      wp_set_object_terms($post_id, $_POST['pickup'], 'pickup', true);
      wp_set_object_terms($post_id, $_POST['event_type'], 'event_type', true);
      
      $post = get_post($post_id);
      $meta = get_post_custom($post_id);
      $categories = array();
      foreach($bookittrans_config['categories'] as $key=>$value) {
        $category = wp_get_post_terms( $post_id, $key );
        $categories[$key] = $category;
      }
      $ary = array(
        'title' => $post->post_title
      );
      foreach($bookittrans_config['fields'] as $key=>$array) {
        $ary[$array['key']] = $meta[$array['key']][0];
      }
      foreach($categories as $tag=>$array) {
        foreach($array as $k=>$v) {
          if(isset($ary[$tag])) $ary[$tag] .= ', ';
          $ary[$tag] .= $v->name;
        }
      }
      
      $headers[] = 'From: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
      $headers[] = 'Bcc: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
      add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
      wp_mail( $_POST['contact_email'], $bookittrans_config['emails']['reservation_email_subject'], bookittrans_tags($bookittrans_config['emails']['reservation_email_template'],$ary), $headers );
      
      wp_redirect( $bookittrans_config['reservation-received-url'] );
      exit;
    }
  }
}
// Send the reservation received email
function email_reservation_received($ID) {
  global $bookittrans_config;
  $post = get_post($ID);
  $meta = get_post_custom($ID);
  $user_name = $meta['contact_name'][0];
  $user_email = $meta['contact_email'][0];
  $subject = $bookittrans_config['emails']['reservation_email_subject'];
  
  $categories = array();
  foreach($bookittrans_config['categories'] as $key=>$value) {
    $category = wp_get_post_terms( $ID, $key );
    $categories[$key] = $category;
  }
  
  $ary = array(
    'title' => $post->post_title
  );
  foreach($bookittrans_config['fields'] as $key=>$array) {
    $ary[$array['key']] = $meta[$array['key']][0];
  }
  foreach($categories as $tag=>$array) {
    foreach($array as $k=>$v) {
      if(isset($ary[$tag])) $ary[$tag] .= ', ';
      $ary[$tag] .= $v->name;
    }
  }
  
  $headers[] = 'From: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
  $headers[] = 'Bcc: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
  add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
  wp_mail( $user_email, $bookittrans_config['emails']['reservation_email_subject'], bookittrans_tags($bookittrans_config['emails']['reservation_email_template'],$ary), $headers );
  return true;
}
function email_reservation_outsource( $ID ) {
  global $bookittrans_config;
  $post = get_post($ID);
  $meta = get_post_custom($ID);
  $outsource = get_the_terms( $ID, 'outsource_companies', '', '', '' );
  if ($outsource) {
    $user_name = $outsource[0]->name;
    $user_email = get_option("_term_type_outsource_companies_".$outsource[0]->term_id);
    $subject = $bookittrans_config['emails']['outsource_reservation_email_subject'];
    $categories = array();
    foreach($bookittrans_config['categories'] as $key=>$value) {
      $category = wp_get_post_terms( $ID, $key );
      $categories[$key] = $category;
    }
    
    $ary = array(
      'title' => $post->post_title
    );
    foreach($bookittrans_config['fields'] as $key=>$array) {
      $ary[$array['key']] = $meta[$array['key']][0];
    }
    foreach($categories as $tag=>$array) {
      foreach($array as $k=>$v) {
        if(isset($ary[$tag])) $ary[$tag] .= ', ';
        $ary[$tag] = $v->name;
      }
    }
    
    $headers[] = 'From: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
    $headers[] = 'Bcc: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
    add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
    wp_mail( $user_name.' <'.$user_email.'>', $bookittrans_config['emails']['outsource_reservation_email_subject'], bookittrans_tags($bookittrans_config['emails']['outsource_reservation_email_template'],$ary), $headers );
    return __('Email successfully sent.', 'bookit');
  } else {
    return __('<b>This reservation isn\'t currently assiged to an outsource company.</b> Be sure an outsource company is selected and the reservation has been saved.', 'bookit');
  }
}
// Send the reservation confirmed email
function email_reservation_confirmed($ID) {
  global $bookittrans_config;
  $post = get_post($ID);
  $meta = get_post_custom($ID);
  $user_name = $meta['contact_name'][0];
  $user_email = $meta['contact_email'][0];
  $subject = $bookittrans_config['emails']['reservation_email_subject'];
  
  $categories = array();
  foreach($bookittrans_config['categories'] as $key=>$value) {
    $category = wp_get_post_terms( $ID, $key );
    $categories[$key] = $category;
  }
  
  $ary = array(
    'title' => $post->post_title
  );
  foreach($bookittrans_config['fields'] as $key=>$array) {
    $ary[$array['key']] = $meta[$array['key']][0];
  }
  foreach($categories as $tag=>$array) {
    foreach($array as $k=>$v) {
      if(isset($ary[$tag])) $ary[$tag] .= ', ';
      $ary[$tag] = $v->name;
    }
  }
  
  $headers[] = 'From: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
  $headers[] = 'Bcc: '.get_bloginfo('admin_name').' <'.get_bloginfo('admin_email').'>';
  add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
  wp_mail( $user_email, $bookittrans_config['emails']['reservation_confirmation_email_subject'], bookittrans_tags($bookittrans_config['emails']['reservation_confirmation_email_confirmed_template'],$ary), $headers );
  return true;
}
// Rewrites email template tags
function bookittrans_tags($html,$ary) {
  $find = array();
  $replace = array();
  foreach($ary as $key=>$value) {
    $find[] = '[['.strtoupper($key).']]';
    $replace[] = $value;
  }
  $html = str_replace($find,$replace,$html);
  return $html;
}
// Add's the plugin's custom post types.
function bookittrans_add_post_types() {
  global $bookittrans_config;
  $post_types = $bookittrans_config['post_types'];
  foreach($post_types as $key=>$value) {
    register_post_type( $key , $value['args'] );
  }
}
// Check for plugin dependencies.
function bookittrans_dependentplugin_check() {
  require_once( ABSPATH . '/wp-admin/includes/plugin.php' );
  if ( is_plugin_active( 'custom-status/index.php' ) && file_exists( WP_PLUGIN_DIR . '/custom-status/index.php' )) {
    require_once ( WP_PLUGIN_DIR . '/custom-status/index.php' );
  } else {
    deactivate_plugins( __FILE__);
    //exit ('Requires the Custom Status plugin to work (<a href="http://wordpress.org/extend/plugins/custom-status/" target="_blank">http://wordpress.org/extend/plugins/custom-status/</a>)');
   }
}
// Add custom post categories.
function bookittrans_add_categories() {
  global $bookittrans_config;
  foreach($bookittrans_config['categories'] as $key=>$value) {
    register_taxonomy($key,$value['post_types'], array(
      'hierarchical' => true,
      'labels' => $value['labels'],
      'show_ui' => true,
      'show_admin_column' => true,
      'query_var' => false,
      'rewrite' => false)
    );
  }
}

// admin_init is triggered before any other hook when a user access the admin area. This hook doesn't provide any parameters, so it can only be used to callback a specified function.
add_action( 'admin_init', 'bookittrans_admin' );
function bookittrans_admin() {
  global $pagenow;
  add_meta_box( 'reservation_details', 'Reservation Details', 'display_resevation_details', 'bookit_reservation', 'normal', 'core' );
  if( $pagenow == 'post.php') {
    add_meta_box( 'reservation_notification_options', 'Notification Options', 'display_notification_options', 'bookit_reservation', 'side', 'core' );
  }
  bookittrans_add_settings();
}
function bookittrans_add_settings() {
  register_setting( 'bookittrans_options', 'bookittrans_reservation_received_url', 'bookittrans_isValidURL' );
  register_setting( 'bookittrans_options', 'bookittrans_default_reservation_status' );
  register_setting( 'bookittrans_options', 'bookittrans_confirmation_email_subject', 'bookittrans_emailSubject' );
  register_setting( 'bookittrans_options', 'bookittrans_reservation_email_subject', 'bookittrans_emailSubject' );
  register_setting( 'bookittrans_options', 'bookit_outsource_reservation_email_subject', 'bookittrans_emailSubject' );
  register_setting( 'bookittrans_options', 'bookittrans_confirmation_email_template' );
  register_setting( 'bookittrans_options', 'bookittrans_reservation_email_template' );
  register_setting( 'bookittrans_options', 'bookittrans_outsource_reservation_email_template' );
}
function bookittrans_emailSubject($value) {
  if(strlen($value) > 80) {
    add_settings_error(
      'bookittrans_confirmation_email_subject',
      'bookittrans_confirmation_email_subject_error',
      'To help avoid spam filters, avoid a email subject with more than 80 characters.',
      'error'
    );
  }
  return $value;
}
function bookittrans_isValidURL($value) {
  $response = wp_remote_get( esc_url_raw( $value ) );
  if (is_wp_error( $response ) ) {
    add_settings_error(
      'bookittrans_reservation_received_url',
      'bookittrans_reservation_received_url_error',
      'Please enter a valid, working URL.',
      'error'
    );
  }
  return $value;
}

// Function that prints out the HTML for the edit screen section.
function display_resevation_details($object) {
  global $bookittrans_config;
  ?>
  <?php
  wp_nonce_field( basename( __FILE__ ), 'bookittrans_nonce' );
  $outsource = get_the_terms( $ID, 'outsource_companies', '', '', '' );
  $term_args=array(
    'hide_empty' => false,
    'orderby' => 'name',
    'order' => 'ASC'
  );
  $terms = get_terms('outsource_companies',$term_args);
  ?>
  <table class="form-table">
    <tbody>
      <tr>
        <td valign="top"><label for="outsource_company"><?php echo __('Outsource Company', 'bookit') ?></label></td>
        <td valign="top">
          <select id="outsource_company" name="tax_input[outsource_companies][]">
            <option value=""><?php echo __('In-House (not outsourced)', 'bookit') ?></option>
            <?php foreach ($terms as $tag) { ?>
            <option value="<?php echo $tag->term_id ?>" <?php if ($outsource[0]->term_id === $tag->term_id): ?>selected="selected"<?php endif; ?>><?php echo $tag->name ?></option>
            <?php } ?>
        </select>
        </td>
      </tr>
    <? foreach($bookittrans_config['fields'] as $key=>$value): ?>
      <tr>
        <td valign="top"><label for="<?=$value['key'] ?>"><?php _e( $value['name'], 'bookittrans' ); ?></label></td>
        <td valign="top">
          <?
          if($value['type'] === 'select') {
            ?>
            <select name="<?=$value['key'] ?>" id="<?=$value['key'] ?>">
              <? foreach($value['options'] as $k=>$v): ?>
              <option value="<?=$k ?>" <? if(get_post_meta( $object->ID, $value['key'], true ) == $k): ?>selected="selected"<? endif; ?>><?=$v ?></option>
              <? endforeach; ?>
            </select>
            <?
          } elseif($value['type'] === 'text' || $value['type'] === 'number'  || $value['type'] === 'tel' || $value['type'] === 'email') {
            ?>
            <input class="<?=$value['class'] ?>" type="<?=$value['type'] ?>" name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" value="<?php echo esc_attr( get_post_meta( $object->ID, $value['key'], true ) ); ?>" <? if($value['type'] === 'number'): ?>min="0"<? endif; ?>>
            <?
          } elseif($value['type'] === 'textarea') {
            ?>
            <textarea name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" cols="50" rows="5" class="<?=$value['class'] ?>"><?php echo esc_attr( get_post_meta( $object->ID, $value['key'], true ) ); ?></textarea>
            <?
          }
          ?>
        </td>
      </tr>
      <? endforeach; ?>
    </tbody>
  </table>
  <?
}
function display_notification_options() {
  ?>
  <p><?=__('Use the buttons below to send notification emails regarding this reservation.') ?> <span class="description"><b><?=__('Remember to save the reservation before sending notification emails.') ?></b></span></p>
  <div id="email_status"></div>
  <div id="notification_options">
    <hr>
    <a href="#" class="button" id="send_reservation_received"><?=__('Email Reservation Received') ?></a>
    <p class="description"><?=__('Sends the user and admin an email stating the reservation has been received.') ?></p>
    <hr>
    <a href="#" class="button" id="send_reservation_confirmed"><?=__('Email Reservation Confirmed') ?></a>
    <p class="description"><?=__('Sends the user and admin an email stating the reservation has been confirmed.') ?></p>
    <hr>
    <a href="#" class="button" id="send_reservation_outsource"><?=__('Email Outsource Reservation') ?></a>
    <p class="description"><?=__('Sends the selected outsource company a email containing the reservationd details.') ?></p>
  </div>
  <script>
  jQuery(function() { 
    jQuery('#send_reservation_received').live('click',function(e) {
      e.preventDefault();
      jQuery('#notification_options .button').addClass('button-disabled');
      jQuery('#email_status').html('<?=__('<div class="updated"><p>Sending, please wait&hellip;</p></div>') ?>');
      
      var data = {
        bookittrans_action: 'send_reservation_received',
        ID: <?=get_the_ID(); ?>
      };
      jQuery.post(ajaxurl, data, function(response) {
        jQuery('#email_status').html('<div class="updated"><p>' + response + '</p></div>');
        jQuery('#notification_options .button').removeClass('button-disabled');
      });
    });
    
    jQuery('#send_reservation_confirmed').live('click',function(e) {
      e.preventDefault();
      jQuery('#notification_options .button').addClass('button-disabled');
      jQuery('#email_status').html('<?=__('<div class="updated"><p>Sending, please wait&hellip;</p></div>') ?>');
      
      var data = {
        bookittrans_action: 'send_reservation_confirmed',
        ID: <?=get_the_ID(); ?>
      };
      jQuery.post(ajaxurl, data, function(response) {
        jQuery('#email_status').html('<div class="updated"><p>' + response + '</p></div>');
        jQuery('#notification_options .button').removeClass('button-disabled');
      });
    });
    
    jQuery('#send_reservation_outsource').live('click',function(e) {
      e.preventDefault();
      jQuery('#notification_options .button').addClass('button-disabled');
      jQuery('#email_status').html('<?=__( '<div class="updated"><p>Sending, please wait&hellip;</p></div>', 'bookittrans') ?>');
      
      var data = {
        bookittrans_action: 'send_reservation_outsource',
        ID: <?=get_the_ID(); ?>
      };
      jQuery.post(ajaxurl, data, function(response) {
        jQuery('#email_status').html('<div class="updated"><p>' + response + '</p></div>');
        jQuery('#notification_options .button').removeClass('button-disabled');
      });
    });
  });
  </script>
  <?
}

// save_post is an action triggered whenever a post or page is created or updated, which could be from an import, post/page edit form, xmlrpc, or post by email.
add_action( 'save_post', 'bookittrans_save_reservation', 10, 2 );
// Callback function when a reservation post is saved.
function bookittrans_save_reservation($post_id, $reservation_details) {
  global $bookittrans_config;
  // Check post type for reservations
  if ( $reservation_details->post_type == 'bookit_reservation' ) {
    /* Get the posted data and sanitize it for use as an HTML class. */
    foreach($bookittrans_config['fields'] as $key=>$value) {

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
add_filter( 'enter_title_here', 'bookittrans_change_enter_title_text', 10, 2 );
function bookittrans_change_enter_title_text( $text, $post ) {
  if( $post->post_type == 'bookit_reservation') {
    return __( 'Enter the reservation confirmation code', 'bookittrans');
  } else {
    return $text;
  }
}
add_filter( 'gettext', 'change_publish_button', 10, 2 );
function change_publish_button( $translation, $text ) {
  if( 'bookit_reservation' == get_post_type())
    if ( $text == 'Publish' )
      return 'Save Reservation';
  return $translation;
}

// Add the reservation shortcode
add_shortcode( 'bookittrans_reservation_form', 'bookittrans_shortcode_reservation_form' );
function bookittrans_shortcode_reservation_form( $atts ) {
  global $bookittrans_config, $post;
  $current_user = wp_get_current_user();
  extract( shortcode_atts( array(
  ), $atts ) );
  $html = '<form id="bookit-reservation" name="bookit-reservation" method="post" action="'.get_permalink().'"><input type="hidden" name="bookittrans_action" value="bookittrans-reservation">'.wp_nonce_field( 'bookittrans_nonce' );
  $events = get_terms( 'event_type', array(
    'orderby'    => 'count',
    'hide_empty' => 0
  ) );
  $vehicles = get_terms( 'vehicle', array(
    'orderby'    => 'count',
    'hide_empty' => 0
  ) );
  ?>
  <? if(isset($_SESSION['bookittrans']['errors'])): ?>
  <div class="message-box-wrapper red">
    <div class="message-box-title"><?=__('Sorry, there was a problem processing your reservation, see below.') ?></div>
    <div class="message-box-content"><ul><?
    foreach($_SESSION['bookittrans']['errors'] as $key=>$value):
      if($value === 'contact_name'):
        echo '<li>'.__('Please enter your <strong>name</strong>.');
      elseif($value === 'contact_phone'):
        echo '<li>'.__('Please enter your <strong>phone number</strong>.');
      elseif($value === 'contact_email'):
        echo '<li>'.__('Please enter your <strong>email address</strong>.');
      elseif($value === 'num_passengers'):
        echo '<li>'.__('Please enter the <strong>number of passengers</strong>.');
      endif;
    endforeach; 
    ?></ul></div>
  </div>
  <?  unset($_SESSION['bookittrans']['errors']); endif; ?>
  <?
  if(isset($_SESSION['bookittrans']['post'])) $bookittrans_config['post'] = $_SESSION['bookittrans']['post'];
  foreach($bookittrans_config['fields'] as $key=>$value) {
    ob_start();
    ?>
    <input type="hidden" name="page" value="<?=get_permalink() ?>">
    <div class="bookit-field <?=$value['key'] ?>">
      <div class="bookit-label"><label for="<?=$value['key'] ?>"><?=$value['name'] ?></label></div>
      <div class="bookit-input">
        <?
        if($value['type'] === 'text' || $value['type'] === 'number'  || $value['type'] === 'tel' || $value['type'] === 'email'): ?>
          <input type="<?=$value['type'] ?>" name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" value="<?=stripslashes($bookittrans_config['post'][$value['key']]) ?>" placeholder="<?=$value['placeholder'] ?>" <? if($value['type'] === 'number'): ?>min="0"<? endif; ?>>
        <? elseif($value['type'] === 'textarea'): ?>
          <textarea name="<?=$value['key'] ?>" id="<?=$value['key'] ?>" cols="50" rows="5"><?=stripslashes($bookittrans_config['post'][$value['key']]) ?></textarea>
        <? elseif($value['type'] === 'select'): ?>
          <select name="<?=$value['key'] ?>" id="<?=$value['key'] ?>">
            <? foreach($value['options'] as $k=>$v): ?>
            <option value="<?=$k ?>" <? if(stripslashes($bookittrans_config['post'][$value['key']]) == $k): ?>selected="selected"<? endif; ?>><?=$v ?></option>
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
    <div class="bookit-label"><label for="pickup"><?=_e('Where would you like to be picked up at?','bookittrans') ?></label></div>
    <div class="bookit-input">
      <input type="text" name="pickup[]" id="pickup" value="<?=stripslashes($bookittrans_config['post']['pickup'][0]) ?>" class="bookit-pickup" placeholder="<?=_e('Enter a address, business name or landmark','bookittrans') ?>">
    </div>
  </div>
  <div class="bookit-field destinations">
    <div class="bookit-label"><label for="destinations"><?=_e('Tell us where you\'d like to be taken:','bookittrans') ?></label></div>
    <div class="bookit-input" id="cell-destinations">
      <input type="text" name="destinations[]" class="bookit-destinations" value="<?=stripslashes($bookittrans_config['post']['destinations'][0]) ?>" placeholder="<?=_e('Enter a address, business name or landmark','bookittrans') ?>"> <a href="#" id="bookittrans_add">Add another destination &raquo;</a>
    </div>
    <script>
    jQuery(function() {
      jQuery('#bookittrans_add').live('click',function(e) {
        e.preventDefault();
        jQuery('#bookittrans_add').before(jQuery("#cell-destinations input").first().clone());
      });
    });
    </script>
  </div>
  <div class="bookit-field vehicle">
    <div class="bookit-label"><label for="vehicle"><?=_e('Select your perferred vehicle:','bookittrans') ?></label></div>
    <div class="bookit-input">
      <select name="vehicle" id="vehicle">
        <? foreach($vehicles as $key=>$value): ?>
        <option value="<?=$value->slug ?>" <? if(stripslashes($bookittrans_config['post']['vehicle']) == $value->slug): ?>selected="selected"<? endif; ?>><?=$value->name ?></option>
        <? endforeach; ?>
      </select>
    </div>
  </div>
  <div class="bookit-field event-type">
    <div class="bookit-label"><label for="event-type"><?=_e('Select a service type:','bookittrans') ?></label></div>
    <div class="bookit-input">
      <select name="event_type" id="event_type">
        <? foreach($events as $key=>$value): ?>
        <option value="<?=$value->slug ?>" <? if(stripslashes($bookittrans_config['post']['event_type']) == $value->slug): ?>selected="selected"<? endif; ?>><?=$value->name ?></option>
        <? endforeach; ?>
      </select>
    </div>
  </div>
  <?
  $html .= ob_get_contents();
  ob_end_clean();
    
  
  $html .= '<input type="submit" value="Submit Reservation" id="submit" name="submit"></form>';
  unset($_SESSION['bookittrans']['post']);
  return $html;
}

// Add nessary JS
add_action('wp_enqueue_scripts', 'bookittrans_add_scipts');
function bookittrans_add_scipts() {
  wp_enqueue_script(
    'jquery_ui',
    plugins_url('/assets/js/jquery-ui-1.10.0.custom.min.js', __FILE__),
    array('jquery')
  );
  wp_enqueue_script(
    'bookittrans',
    plugins_url('/assets/js/script.js', __FILE__),
    array('jquery_ui')
  );
  wp_register_style( 'jquery_ui_benmarshall', plugins_url('/assets/css/benmarshall/jquery-ui-1.10.0.custom.min.css', __FILE__) );
  wp_enqueue_style( 'jquery_ui_benmarshall' );
  
  $categories = get_terms( 'destinations', array(
    'orderby'    => 'count',
    'hide_empty' => 0
   ) );
  wp_localize_script( 'bookittrans', 'bookit', $categories );
}

add_action('wp_logout', 'bookittrans_end_session');
add_action('wp_login', 'bookittrans_end_session');
function bookittrans_end_session() {
  unset($_SESSION['bookittrans']);
}
function bookittrans_start_session() {
  if(!session_id()) {
    session_start();
  }
}

function bookittrans_randString($length=10, $charset='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') {
  $str = '';
  $count = strlen($charset);
  while ($length--) {
    $str .= $charset[mt_rand(0, $count-1)];
  }
  return $str;
}


add_action( 'admin_menu', 'bookit_admin_menu' );
function bookit_admin_menu() {
  add_options_page( 'Book It! Transportation Settings', 'Book It! Transportation', 'manage_options', 'bookittrans', 'bookittrans_options' );
  remove_meta_box('outsource_companiesdiv', 'bookit_reservation', 'side');
}
function bookittrans_options() {
  if ( !current_user_can( 'manage_options' ) )  {
    wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
  }
  include( plugin_dir_path( __FILE__ ) . 'inc/options.php');
}







/*
 * Example code showing how to hook WordPress to add fields to the taxonomny term edit screen.
 * 
 * This example is meant to show how, not to be a drop in example.
 *
 * This example was written in response to this question:
 *
 *    http://lists.automattic.com/pipermail/wp-hackers/2010-August/033671.html
 *
 * By:
 *
 *    Mike Schinkel (http://mikeschinkel.com/custom-wordpress-plugins/)
 *
 * NOTE:
 *
 *    This could easily become a plugin if it were fleshed out.
 *    A class with static methods was used to minimize the variables & functions added to the global namespace.
 *    wp_options was uses with one option be tax/term instead of via a serialize array because it aids in retrival
 *    if there get to be a large number of tax/terms types. A taxonomy/term meta would be the prefered but WordPress
 *    does not have one.
 *
 * This example is licensed GPLv2.
 *
 */

// These are helper functions you can use elsewhere to access this info
function get_taxonomy_term_type($taxonomy,$term_id) {
  return get_option("_term_type_{$taxonomy}_{$term->term_id}");
}
function update_taxonomy_term_type($taxonomy,$term_id,$value) {
  update_option("_term_type_{$taxonomy}_{$term_id}",$value);
}

//This initializes the class.
TaxonomyTermTypes::on_load();

//This should be called in your own code. This example uses two taxonomies: 'region' & 'opportunity'
TaxonomyTermTypes::register_taxonomy(array('outsource_companies'));

class TaxonomyTermTypes {
  //This initializes the hooks to allow saving of the
  static function on_load() {
    add_action('created_term',array(__CLASS__,'term_type_update'),10,3);
    add_action('edit_term',array(__CLASS__,'term_type_update'),10,3);
  }
  //This initializes the hooks to allow adding the dropdown to the form fields
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
    // This a table row with the drop down for an edit screen
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
  // These hooks are called after adding and editing to save $_POST['tag-term']
  static function term_type_update($term_id, $tt_id, $taxonomy) {
    if (isset($_POST['company_email'])) {
      update_taxonomy_term_type($taxonomy,$term_id,$_POST['company_email']);
    }
  }
}

add_filter('manage_edit-bookit_reservation_columns', 'add_new_bookit_reservation_columns');
function add_new_bookit_reservation_columns($bookit_reservation_columns) {
  $bookit_reservation_columns['cb'] = '<input type="checkbox" />';
  $bookit_reservation_columns['title'] = _x('Confirmation Code', 'column name');
  $bookit_reservation_columns['date_reserved'] = __('Date Reserved');
  return $bookit_reservation_columns;
}

add_action('manage_bookit_reservation_posts_custom_column', 'manage_bookit_reservation_columns', 10, 2);
function manage_bookit_reservation_columns($column_name, $id) {
  global $post;
  switch ($column_name) {
    case 'date_reserved':
      $month = get_post_meta( $post->ID , 'month' , true );
      $date = get_post_meta( $post->ID , 'date' , true );
      $year = get_post_meta( $post->ID , 'year' , true );
      $time = get_post_meta( $post->ID , 'time' , true );
      echo date('M. j, Y g:ia', strtotime($month.'-'.$date.'-'.$year.' '.$time));
      break;
    default:
      break;
  }
}
