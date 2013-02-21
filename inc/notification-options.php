<?php
/*
Book It! Transportation 1.0.4
http://www.benmarshall.me/book-it-transportation/
*/
?>
<p><?=__('Use the buttons below to send notification emails regarding this reservation.') ?> <span class="description"><b><?=__('Remember to save the reservation before sending notification emails.') ?></b></span></p>
<div id="email_status"></div>
<div id="notification_options">
  <hr>
  <a href="#" class="button" id="send_new_reservation_email"><?=__('Email Reservation Received') ?></a>
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
  jQuery('#send_new_reservation_email').live('click',function(e) {
    e.preventDefault();
    jQuery('#notification_options .button').addClass('button-disabled');
    jQuery('#email_status').html('<?=__('<div class="updated"><p>Sending, please wait&hellip;</p></div>') ?>');

    var data = {
      bookit_action: 'send_new_reservation_email',
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
      bookit_action: 'send_reservation_confirmed',
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
    jQuery('#email_status').html('<?=__( '<div class="updated"><p>Sending, please wait&hellip;</p></div>', 'bookit') ?>');

    var data = {
      bookit_action: 'send_reservation_outsource',
      ID: <?=get_the_ID(); ?>
    };
    jQuery.post(ajaxurl, data, function(response) {
      jQuery('#email_status').html('<div class="updated"><p>' + response + '</p></div>');
      jQuery('#notification_options .button').removeClass('button-disabled');
    });
  });
});
</script>