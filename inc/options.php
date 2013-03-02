<?php
/*
 * Book It! Transportation 1.0.6
 * http://www.benmarshall.me/book-it-transportation/
 */
$plugin = get_plugin_data( str_replace('inc/','',plugin_dir_path( __FILE__)).'bookittransportation.php');
$changelog = trim(str_replace('== Changelog ==','',file_get_contents(str_replace('inc/','',plugin_dir_path( __FILE__)).'readme.txt')));
?>
<div class="wrap">
  <?php screen_icon(); ?>
  <form action="options.php" method="post" id="bookit_options_form" name="bookit_options_form">
  <?php settings_fields('bookit_options'); ?>
  <div style="float: right;margin-top:10px"><em><?php echo __('Something not work right? Have a feature request?') ?></em>&nbsp;&nbsp;&nbsp;<a href="http://www.benmarshall.me/bugs/" target="_blank" class="button button-primary"><?php echo __( 'Submit a Bug/Feature Request', 'bookit' )?></a></div>
  <h2><?php echo __('Book It! Transportation') ?> &raquo; Settings</h2>
  <hr>
  <?php echo file_get_contents('http://www.benmarshall.me/api/?id=2')?>
  <div style="float:left;width:60%;">
    <h3 class="title"><?php echo __( 'General Settings', 'bookit' ) ?></h3>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="bookit_show_love"><?php echo __('Show \'Powered by\' Link') ?></label>
        </th>
        <td>
          <input name="bookit_show_love" type="checkbox" id="bookit_show_love" value="show"<?php if( get_option('bookit_show_love') == 'show' ): ?> checked="checked"<?php endif?>>
          <p class="description"><?php echo __('Like the <a href="http://www.benmarshall.me/book-it-transportation/" target="_blank">Book It! Transportation WordPress Plugin</a>? Show some love by leaving the powered by link on the page.') ?></p>
        </td>
      </tr>
    </table>
    <h3 class="title"><?php echo __( 'Reservation Settings', 'bookit' ) ?></h3>
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="bookit_reservation_received_url"><?php echo __('Reservation Received URL') ?></label>
        </th>
        <td>
          <input name="bookit_reservation_received_url" type="text" id="bookit_reservation_received_url" value="<?php echo get_option('bookit_reservation_received_url'); ?>" class="regular-text">
          <p class="description"><?php echo __('The URL the user is directed to after they\'ve submitted the reservation form (e.g. thank you page, confirmation pending page, conversion page, etc.)') ?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_reservation_failed_url"><?php echo __('Reservation Failed URL') ?></label>
        </th>
        <td>
          <input name="bookit_reservation_failed_url" type="text" id="bookit_reservation_failed_url" value="<?php echo get_option('bookit_reservation_failed_url'); ?>" class="regular-text">
          <p class="description"><?php echo __('The URL the user is directed to if the new reservation email failed to send out (the reservation is still saved in the database).') ?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_default_reservation_status"><?php echo __('Default Reservation Status') ?></label>
        </th>
        <td>
          <select name="bookit_default_reservation_status" id="bookit_default_reservation_status">
            <option value="confirmed"<? if(get_option('bookit_default_reservation_status') == 'confirmed'): ?>selected="selected"<? endif; ?>><?php echo __('Confirmed') ?></option>
            <option value="pending-review"<? if(get_option('bookit_default_reservation_status') == 'pending-review'): ?>selected="selected"<? endif; ?>><?php echo __('Pending Review') ?></option>
          </select>
          <p class="description"><?php echo __('Select the default status for new reservations.') ?></p>
        </td>
      </tr>
    </table>
  </div>
  <div style="float:right; width:40%;">
    <div class="metabox-holder">
      <div class="postbox">
        <h3 class="hndle"><?php echo $plugin['Name'] ?> <?php echo __( 'WordPress Plugin', 'bookit') ?></h3>
        <div class="inside">
          <b><?php echo __( 'Author:', 'bookit' ) ?></b> <a href="http://www.benmarshall.me/book-it-transportation/" target="_blank">Ben Marshall</a><br>
          <b><?php echo __( 'Version:', 'bookit' ) ?></b> <?php echo $plugin['Version'] ?><br>
          <b><?php echo __( 'Last Updated:', 'bookit' ) ?></b> <?php echo date("F d, Y g:i:sa", filemtime(__FILE__)) ?>
          <?php echo file_get_contents('http://www.benmarshall.me/api/?id=1')?>
          <h4><?php echo __( 'Available Post &amp; Page Shortcodes', 'bookit' )?></h4>
          <table>
            <tbody>
              <tr>
                <td><code>[bookit_reservation_form]</code></td><td><em><?php echo __( 'Renders the reservation form.', 'bookit' ) ?></em></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="clear"></div>
  <hr>
  <h3 class="title"><?php echo __('Email Settings') ?></h3>
  <div style="float:left;width:60%;">
    <table class="form-table">
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_default_subject"><?php echo __('Default Email Subject') ?></label>
        </th>
        <td>
          <input name="bookit_emails_default_subject" type="text" id="bookit_emails_default_subject" value="<?php echo get_option( 'bookit_emails_default_subject', 'bookit' )?>" class="regular-text">
          <p class="description"><?php echo __( 'Enter the default email subject if one isn\'t set.', 'bookit' )?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_new_reservation_subject"><?php echo __('New Reservation Email Subject') ?></label>
        </th>
        <td>
          <input name="bookit_emails_new_reservation_subject" type="text" id="bookit_emails_new_reservation_subject" value="<?php echo get_option( 'bookit_emails_new_reservation_subject', 'bookit' )?>" class="regular-text">
          <p class="description"><?php echo __( 'Enter the subject of the email for new reservation bookings.', 'bookit' )?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_new_reservation_template"><?php echo __('New Reservation Email Template') ?></label>
        </th>
        <td>
          <textarea name="bookit_emails_new_reservation_template" id="bookit_emails_new_reservation_template" rows="10" class="large-text code"><?php echo get_option( 'bookit_emails_new_reservation_template', 'bookit')?></textarea>
          <p class="description"><?php echo __( 'This is the email that get\'s sent out for new reservations. HTML accepted.')?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_reservation_confirmed_subject"><?php echo __('Reservation Confirmed Subject') ?></label>
        </th>
        <td>
          <input name="bookit_emails_reservation_confirmed_subject" type="text" id="bookit_emails_reservation_confirmed_subject" value="<?php echo get_option('bookit_emails_reservation_confirmed_subject'); ?>" class="regular-text">
          <p class="description"><?php echo __('The subject of the reservation confirmation email.') ?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_reservation_confirmed_template"><?php echo __('Reservation Confirmed Template') ?></label>
        </th>
        <td>
          <textarea name="bookit_emails_reservation_confirmed_template" id="bookit_emails_reservation_confirmed_template" rows="10" class="large-text code"><?php echo get_option('bookit_emails_reservation_confirmed_template'); ?></textarea>
          <p class="description"><?php echo __('This is the email that get\'s sent out for confirmed reservations.') ?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_outsource_subject"><?php echo __('Reservation Outsource Subject') ?></label>
        </th>
        <td>
          <input name="bookit_emails_outsource_subject" type="text" id="bookit_emails_outsource_subject" value="<?php echo get_option('bookit_emails_outsource_subject'); ?>" class="regular-text">
          <p class="description"><?php echo __('The subject of the reservation email that get\'s sent to outsource companies.') ?></p>
        </td>
      </tr>
      <tr valign="top">
        <th scope="row">
          <label for="bookit_emails_outsource_template"><?php echo __('Reservation Outsource Template') ?></label>
        </th>
        <td>
          <textarea name="bookit_emails_outsource_template" id="bookit_emails_outsource_template" rows="10" class="large-text code"><?php echo get_option('bookit_emails_outsource_template'); ?></textarea>
          <p class="description"><?php echo __('This is the email that contains reservation information for outsource companies.', 'bookit') ?></p>
        </td>
      </tr>
    </table>
  </div>
  <div style="float:right; width:40%;">
    <div class="metabox-holder">
      <div class="postbox">
        <h3 class="hndle"><?php echo __('Available Email Template Shortcodes', 'bookit') ?></h3>
        <div class="inside">
          <table>
            <tbody>
              <tr>
                <td><code>[[TITLE]]</code></td><td><em><?php echo __('the reservation confirmation code', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[RESERVATION_DATE_FULLTEXT]]</code></td><td><em><?php echo __('the reservation\'s full text date<br>(i.e. Saturday, May 11, 2014 9:30am)', 'bookit') ?></em></td>
              </tr>
              <tr>
              <tr>
                <td><code>[[MONTH]]</code></td><td><em><?php echo __('the month (number) the reservation is booked for', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[DATE]]</code></td><td><em><?php echo __('the date the reservation is booked for', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[YEAR]]</code></td><td><em><?php echo __('the year the reservation is booked for', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[TIME]]</code></td><td><em><?php echo __('the time the reservation is booked for', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[CONTACT_NAME]]</code></td><td><em><?php echo __('the reservation\'s contact name', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[CONTACT_PHONE]]</code></td><td><em><?php echo __('the reservation\'s contact phone number', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[CONTACT_EMAIL]]</code></td><td><em><?php echo __('the reservation\'s contact email address', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[PICKUP]]</code></td><td><em><?php echo __('the pickup location for the reservation', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[DESTINATIONS]]</code></td><td><em><?php echo __('the destination(s) location for the reservation', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[VEHICLE]]</code></td><td><em><?php echo __('the vehicle booked for the reservation', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[EVENT_TYPE]]</code></td><td><em><?php echo __('the event type for the reservation', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[NUM_PASSENGERS]]</code></td><td><em><?php echo __('the number of passengers booked for the reservation', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[NUM_HOURS]]</code></td><td><em><?php echo __('the number of hours booked for the reservation', 'bookit') ?></em></td>
              </tr>
              <tr>
                <td><code>[[INSTRUCTIONS]]</code></td><td><em><?php echo __('client instructions for the reservation', 'bookit') ?></em></td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="clear"></div>
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
        <h4><?php echo __('Change Log') ?></h4>
        <?php echo nl2br($changelog) ?>
      </div>
    </div>
  </div>
</div>
