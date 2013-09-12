<?php
/**
 * @package Book_It
 * @version 2.0
 */
?>
<div>
	<div class="misc-pub-section">
		<p><?php echo __('Send reservation emails below. Be sure to save the reservation if changes are made below sending any emails.', 'bookit') ?></p>
	</div>
	<p class="text-center">
		<a href="#" class="button"><?php echo __('Received', 'bookit') ?></a>
		<a href="#" class="button"><?php echo __('Confirmed', 'bookit') ?></a>
		<a href="#" class="button" id="sendDetails"><?php echo __('Email Details', 'bookit') ?></a>
	</p>
	<div id="emailDetails" style="display: none">
		<label for="recipient">
			<b><?php echo __('Send to', 'bookit') ?>:</b>
			<input type="text" id="recipient">
		</label>
		<p class="description"><?php echo __('Start typing an email address or outsource company to send the reservation details to.', 'bookit') ?></p>

		<label for="template">
			<b><?php echo __('Template', 'bookit') ?>:</b>
			<select id="template">
				<option value="default"><?php echo __('Default') ?></option>
			</select>
			<p class="description"><?php echo __('Select a email template to use.', 'bookit') ?></p>
		</label>

		<a href="#" class="button" id="sendEmail"><?php echo __('Send') ?></a>
	</div>
</div>

<script>
(function($) {
	$(function() {
		$('#sendDetails').bind('click', function(e) {
			e.preventDefault();
			$('#emailDetails').slideToggle();
		});

		$('#sendEmail').bind('click', function(e) {
			
		});
	});
})(jQuery);
</script>