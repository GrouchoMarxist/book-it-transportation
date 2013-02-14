<?php
/*
 * Book It! Transportation 1.0.01
 * http://www.benmarshall.me/book-it-transportation/
 */
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
  </table>
  <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Save Changes') ?>"></p>
  </form>
</div>
