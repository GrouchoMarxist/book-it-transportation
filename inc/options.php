<?php
/*
 * Book It! Transportation 1.0.2
 * http://www.benmarshall.me/book-it-transportation/
 */
$plugin = get_plugin_data( str_replace('inc/','',plugin_dir_path( __FILE__)).'bookittransportation.php');
$changelog = trim(str_replace('== Changelog ==','',file_get_contents(str_replace('inc/','',plugin_dir_path( __FILE__)).'readme.txt')));
?>
<div class="wrap">
  <?php screen_icon(); ?>
  <form action="options.php" method="post" id="bookittrans_options_form" name="bookittrans_options_form">
  <?php settings_fields('bookittrans_options'); ?>
  <h2><?php echo __('Book It! Transportation') ?> &raquo; Settings</h2>
  <h3 class="title"><?php echo __('Reservation Settings') ?></h3>
  <table class="form-table">
    <tr valign="top">
      <th scope="row">
        <label for="bookittrans_reservation_received_url"><?php echo __('Reservation Received URL') ?></label>
      </th>
      <td>
        <input name="bookittrans_reservation_received_url" type="text" id="bookittrans_reservation_received_url" value="<?php echo get_option('bookittrans_reservation_received_url'); ?>" class="regular-text">
        <p class="description"><?php echo __('The URL the user is directed to after they\'ve submitted the reservation form (e.g. thank you page, confirmation pending page, conversion page, etc.)') ?></p>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="bookittrans_default_reservation_status"><?php echo __('Default Reservation Status') ?></label>
      </th>
      <td>
        <select name="bookittrans_default_reservation_status" id="bookittrans_default_reservation_status">
          <option value="confirmed"<? if(get_option('bookittrans_default_reservation_status') == 'confirmed'): ?>selected="selected"<? endif; ?>><?php echo __('Confirmed') ?></option>
          <option value="pending-review"<? if(get_option('bookittrans_default_reservation_status') == 'pending-review'): ?>selected="selected"<? endif; ?>><?php echo __('Pending Review') ?></option>
        </select>
        <p class="description"><?php echo __('Select the default status for new reservations.') ?></p>
      </td>
    </tr>
  </table>
  <h3 class="title"><?php echo __('Email Settings') ?></h3>
  <table class="form-table">
    <tr valign="top">
      <th scope="row">
        <label for="bookittrans_reservation_email_subject"><?php echo __('New Reservation Subject') ?></label>
      </th>
      <td>
        <input name="bookittrans_reservation_email_subject" type="text" id="bookittrans_reservation_email_subject" value="<?php echo get_option('bookittrans_reservation_email_subject'); ?>" class="regular-text">
        <p class="description"><?php echo __('The subject of the email that get\'s sent for new reservation bookings.') ?></p>
      </td>
    </tr>
    <tr valign="top">
      <th scope="row">
        <label for="bookittrans_confirmation_email_subject"><?php echo __('Reservation Confirmed Subject') ?></label>
      </th>
      <td>
        <input name="bookittrans_confirmation_email_subject" type="text" id="bookittrans_confirmation_email_subject" value="<?php echo get_option('bookittrans_confirmation_email_subject'); ?>" class="regular-text">
        <p class="description"><?php echo __('The subject of the email that get\'s sent for reservation confirmations.') ?></p>
      </td>
    </tr>
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes') ?>"></p>
  </form>
  <div class="metabox-holder">
    <div class="postbox">
      <h3 class="hndle"><?php echo __('Book It! Transportation Details') ?></h3>
      <div class="inside">
        <div style="float: right;">
          <script type="text/javascript"><!--
          google_ad_client = "ca-pub-6102402008946964";
          /* Book It! Transportation Medium Rectangle */
          google_ad_slot = "3498616466";
          google_ad_width = 300;
          google_ad_height = 250;
          //-->
          </script>
          <script type="text/javascript"
          src="http://pagead2.googlesyndication.com/pagead/show_ads.js">
          </script>
        </div>
        <b><?php echo __('Author:') ?></b> <a href="http://www.benmarshall.me/book-it-transportation/" target="_blank">Ben Marshall</a><br>
        <b><?php echo __('Version:') ?></b> <?php echo $plugin['Version'] ?><br>
        <b><?php echo __('Last Updated:') ?></b> <?php echo date("F d, Y g:i:sa", filemtime(__FILE__)) ?>
        <h4><?php echo __('Change Log') ?></h4>
        <?php echo nl2br($changelog) ?>
      </div>
    </div>
  </div>
</div>
