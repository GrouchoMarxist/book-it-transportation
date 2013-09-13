<?php
/**
 * @package Book_It
 * @version 2.0
 */
?>
<div><div class="misc-pub-section">
	<?php if ( get_post_meta($post->ID, 'bookit_reservation_date', true) ): ?><div class="bookit-msg">
		<p><?php echo __('Booked', 'bookit') ?> <abbr class="timeago" title="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_reservation_date', true)) ?> <?php echo esc_attr(get_post_meta($post->ID, 'bookit_pickup_time', true)) ?>"></abbr>.</p>
	</div><?php endif; ?>

	<div class="bookit-fourth">
		<p><label for="reservation-date">
			<b><?php _e( 'Reservation Date', 'bookit' ); ?>:</b><br>
			<input type="date" name="bookit_reservation_date" id="reservation-date" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_reservation_date', true)) ?>">
		</label></p>

		<p><label for="reservation-status">
			<b><?php _e( 'Reservation Status', 'bookit' ); ?>:</b><br>
			<?php
			$terms = get_the_terms( $ID, 'bookit_reservation_status', '', '', '' );
		  $term_args = array(
		    'hide_empty' => false,
		    'orderby' => 'name',
		    'order' => 'ASC'
		  );
		  $reservation_statuses = get_terms('bookit_reservation_status', $term_args);
		  if ( count($reservation_statuses) > 0 ):
	  		?>
				<select name="tax_input[bookit_reservation_status][]" id="reservation-status">
					<?php foreach ( $reservation_statuses as $key => $obj ): ?>
						<option value="<?php echo $obj->name ?>" <?php if ($terms[0]->name === $obj->name): ?>selected="selected"<?php endif; ?>><?php echo $obj->name ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<?php echo __('No') ?> <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=bookit_reservation_status&post_type=bookit_reservation' ) ?>"><?php echo __('reservation statuses') ?></a> <?php echo __('found.') ?>
			<?php endif; ?></label></p>
	</div>
	<div class="bookit-fourth">
		<p><label for="pickup-time">
			<b><?php _e( 'Pickup Time', 'bookit' ); ?>:</b><br>
			<input type="time" name="bookit_pickup_time" id="pickup-time" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_pickup_time', true)) ?>">
		</label></p>

		<p><label for="event-type">
			<b><?php _e( 'Event Type', 'bookit' ); ?>:</b><br>
			<?php
			$terms = get_the_terms( $ID, 'bookit_event_type', '', '', '' );
		  $term_args = array(
		    'hide_empty' => false,
		    'orderby' => 'name',
		    'order' => 'ASC'
		  );
		  $event_types = get_terms('bookit_event_type', $term_args);
		  if ( count($event_types) > 0 ):
	  		?>
				<select name="tax_input[bookit_event_type][]" id="event-type">
					<?php foreach ( $event_types as $key => $obj ): ?>
						<option value="<?php echo $obj->name ?>" <?php if ($statuses[0]->name === $obj->name): ?>selected="selected"<?php endif; ?>><?php echo $obj->name ?></option>
					<?php endforeach; ?>
				</select>
			<?php else: ?>
				<?php echo __('No') ?> <a href="<?php echo admin_url( 'edit-tags.php?taxonomy=bookit_event_type&post_type=bookit_reservation' ) ?>"><?php echo __('event types') ?></a> <?php echo __('found.') ?>
			<?php endif; ?></label></p>
	</div>
	<div class="bookit-fourth">
		<p><label for="reservation-hours">
			<b><?php _e( 'Booked For', 'bookit' ); ?>:</b><br>
			<input type="number" min="1" name="bookit_reservation_hours" id="reservation-hours" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_reservation_hours', true)) ?>" class="small-text" > <?php echo __('hours', 'bookit') ?>
		</label></p>
	</div>
	<div class="bookit-fourth">
		<p><label for="reservation-num-passengers">
			<b><?php _e( 'Number of Passengers', 'bookit' ); ?>:</b><br>
			<input type="number" min="1" name="bookit_num_passengers" id="reservation-num-passengers" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_num_passengers', true)) ?>" class="small-text" >
		</label></p>
	</div>
	<div class="clear"></div>
</div>

<div class="misc-pub-section">
	<div class="bookit-half">
		<p><label for="contact-name">
			<b><?php _e( 'Contact Name', 'bookit' ); ?>:</b><br>
			<input type="text" name="bookit_contact_name" id="contact-name" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_contact_name', true)) ?>" class="regular-text">
		</label></p>

		<p><label for="contact-phone">
			<b><?php _e( 'Phone', 'bookit' ); ?>:</b><br>
			<input type="tel" name="bookit_contact_phone" id="contact-phone" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_contact_phone', true)) ?>" class="regular-text">
		</label></p>
	</div>
	<div class="bookit-half">
		<p><label for="contact-email">
			<b><?php _e( 'Email', 'bookit' ); ?>:</b><br>
			<input type="email" name="bookit_contact_email" id="contact-email" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_contact_email', true)) ?>" class="regular-text">
		</label></p>
	</div>
	<div class="clear"></div>
</div></div>

<div class="misc-pub-section">
	<div class="bookit-half">
		<p><label for="pickup-location">
			<b><?php _e( 'Pickup Location', 'bookit' ); ?>:</b><br>
			<input type="text" name="bookit_pickup_location" id="pickup-location" value="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_pickup_location', true)) ?>" class="regular-text"></label> <a href="#" data-location="<?php echo esc_attr(get_post_meta($post->ID, 'bookit_pickup_location', true)) ?>" class="changeMap"><i class="icon-map-marker bookit-link-icon"></i></a></p>

			<p><iframe width="100%" height="300" frameborder="0" scrolling="no" marginheight="0" marginwidth="0" src="//maps.google.com/maps?f=l&amp;hl=en&amp;geocode=&amp;q=<?php echo esc_attr(get_post_meta($post->ID, 'bookit_pickup_location', true)) ?>&amp;ie=UTF8&amp;z=12&amp;t=m&amp;iwloc=addr&amp;output=embed" id="currentMap"></iframe></p>
	</div>
	<div class="bookit-half">
		<div style="padding-left:20px;">
			<p><b><?php echo __('Destination(s)', 'bookit') ?>:</b><br>
				<?php echo __('Enter the destination(s) below in order of arrival.', 'bookit') ?></p>

			<?php
			$destinations = json_decode(get_post_meta($post->ID, 'bookit_destination', true));
			?>
			<div id="destinationList">
				<?php if ( count($destinations) > 0 ): foreach ( $destinations as $key => $loc ): ?>
					<div class="bookit-destination misc-pub-section">
						<label for="destination-<?php echo $key ?>">
						<span class="num"><?php echo ($key + 1) ?></span> <input type="text" name="bookit_destination[<?php echo $key ?>]" id="destination-<?php echo $key ?>" value="<?php echo esc_attr($destinations[$key]) ?>" class="regular-text"></label>
						<a href="#" data-location="<?php echo esc_attr($destinations[$key]) ?>" class="changeMap"><i class="icon-map-marker bookit-link-icon"></i></a>
					</div>
				<?php endforeach; else: ?>
					<div class="bookit-destination misc-pub-section">
						<label for="destination-1">
						<span class="num">1</span> <input type="text" name="bookit_destination[]" id="destination-1" class="regular-text"></label>
					</div>
				<?php endif; ?>
			</div>
			<p><a href="#" id="newDestination" class="button"><?php echo __('Add', 'bookit') ?></a></p>
		</div>
	</div>
	<div class="clear"></div>
</div>


<script>
(function($) {
	$(function() {
		$.timeago.settings.allowFuture = true;
		$('.timeago').timeago();

		$('#newDestination').bind('click', function(e) {
			e.preventDefault();
			var num = $('.bookit-destination').length,
				field = $('.bookit-destination:eq(0)').clone();
			$('label', field).attr('for', 'destination-' + (num + 1));
			$('.num', field).html(num + 1);
			$('input', field).attr('id', 'destination-' + (num + 1)).val('').attr('name', 'bookit_destination[' + num + ']');
			$('#destinationList').append(field);
		});

		$('body').delegate('.changeMap', 'click', function(e) {
			e.preventDefault();
			var location = $(this).data('location');
			$('#currentMap').attr('src', '//maps.google.com/maps?f=l&amp;hl=en&geocode=&q=' + location + '&ie=UTF8&z=12&t=m&iwloc=addr&output=embed');
		});
	});
})(jQuery);
</script>