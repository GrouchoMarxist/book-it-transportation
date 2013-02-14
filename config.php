<?php
/*
Book It! Transportation 1.0.01
http://www.benmarshall.me/book-it-transportation/
*/

// An array to hold the plugin's settings
$bookittrans_config = array();

// Generates an array for the available dates to choose from
$bookittrans_config['days'] = array();
for($i = 1; $i <= 31; $i++) {
  $bookittrans_config['days'][$i] = $i;
}

// Generates an array for the available years to choose from
$bookittrans_config['years'] = array();
for($i = date('Y'); $i <= date('Y') + 1; $i++) {
  $bookittrans_config['years'][$i] = $i;
}

// The available times to choose from.
$bookittrans_config['times'] = array(
  '01:00'=>'1:00am',
  '01:30'=>'1:30am',
  '02:00'=>'2:00am',
  '02:30'=>'2:30am',
  '03:00'=>'3:30am',
  '03:30'=>'3:30am',
  '04:00'=>'4:00am',
  '04:30'=>'4:30am',
  '05:00'=>'5:00am',
  '05:30'=>'5:30am',
  '06:00'=>'6:00am',
  '06:30'=>'6:30am',
  '07:00'=>'7:00am',
  '07:30'=>'7:30am',
  '08:00'=>'8:00am',
  '08:30'=>'8:30am',
  '09:00'=>'9:00am',
  '09:30'=>'9:30am',
  '10:00'=>'10:00am',
  '10:30'=>'10:30am',
  '11:00'=>'11:00am',
  '11:30'=>'11:30am',
  '12:00'=>'12:00pm',
  '12:30'=>'12:30pm',
  '13:00'=>'1:00pm',
  '13:30'=>'1:30pm',
  '14:00'=>'2:00pm',
  '14:30'=>'2:30pm',
  '15:00'=>'3:00pm',
  '15:30'=>'3:30pm',
  '16:00'=>'4:00pm',
  '16:30'=>'4:30pm',
  '17:00'=>'5:00pm',
  '17:30'=>'5:30pm',
  '18:00'=>'6:00pm',
  '18:30'=>'6:30pm',
  '19:00'=>'7:00pm',
  '19:30'=>'7:30pm',
  '20:00'=>'8:00pm',
  '20:30'=>'8:30pm',
  '21:00'=>'9:00pm',
  '21:30'=>'9:30pm',
  '22:00'=>'10:00pm',
  '22:30'=>'10:30pm',
  '23:00'=>'11:00pm',
  '23:30'=>'11:30pm',
  '24:00'=>'12:00pm',
  '24:30'=>'12:30pm'
);

// Reservation confirmation email template
$bookittrans_config['emails']['reservation_confirmation_email_subject'] = 'Your reservation has beed confirmed';
$bookittrans_config['emails']['reservation_confirmation_email_confirmed_template'] = '
<p><strong>Thanks for choosing Austin Choice Limousines to book your next trip!</strong> Your reservation has been confirmed.</p>
<p>Review your reservation details below:</p>
<table>
<tr>
<td>Confirmation ID:</td>
<td><strong>[[TITLE]]</strong></td>
</tr>
<tr>
<td>Reservation Date:</td>
<td>[[MONTH]][[DATE]],[[YEAR]] [[TIME]]</td>
</tr>
<tr>
<td>Contact Name:</td>
<td>[[CONTACT_NAME]]</td>
</tr>
<tr>
<td>Contact Phone:</td>
<td>[[CONTACT_PHONE]]</td>
</tr>
<tr>
<td>Contact Email:</td>
<td>[[CONTACT_EMAIL]]</td>
</tr>
<tr>
<td>Pickup Location:</td>
<td>[[PICKUP]]</td>
</tr>
<tr>
<td>Destination(s):</td>
<td>[[DESTINATIONS]]</td>
</tr>
<tr>
<td>Perferred Vechicle:</td>
<td>[[VEHICLE]]</td>
</tr>
<tr>
<td>Event Type:</td>
<td>[[EVENT_TYPE]]</td>
</tr>
<tr>
<td>Number of Passengers:</td>
<td>[[NUM_PASSENGERS]]</td>
</tr>
<tr>
<td>Number of Hours:</td>
<td>[[NUM_HOURS]]</td>
</tr>
</table>
<hr>
Instructions:<br><br>[[INSTRUCTIONS]]
';

// Reservation email template
$bookittrans_config['emails']['reservation_email_subject'] = 'We\'ve received your reservation request';
$bookittrans_config['emails']['reservation_email_template'] = '
<p><strong>Thanks for choosing Austin Choice Limousines to book your next trip!</strong> We\'ve received your reservation booking and will contact you shorty for confirmation. Most of the time we can do this very quickly, but we may need to contact you for additional information if needed before we can confirm your booking.</p>
<p>Review your reservation details below:</p>
<table>
<tr>
<td>Confirmation ID:</td>
<td><strong>[[TITLE]]</strong></td>
</tr>
<tr>
<td>Reservation Date:</td>
<td>[[MONTH]][[DATE]],[[YEAR]] [[TIME]]</td>
</tr>
<tr>
<td>Contact Name:</td>
<td>[[CONTACT_NAME]]</td>
</tr>
<tr>
<td>Contact Phone:</td>
<td>[[CONTACT_PHONE]]</td>
</tr>
<tr>
<td>Contact Email:</td>
<td>[[CONTACT_EMAIL]]</td>
</tr>
<tr>
<td>Pickup Location:</td>
<td>[[PICKUP]]</td>
</tr>
<tr>
<td>Destination(s):</td>
<td>[[DESTINATIONS]]</td>
</tr>
<tr>
<td>Perferred Vechicle:</td>
<td>[[VEHICLE]]</td>
</tr>
<tr>
<td>Event Type:</td>
<td>[[EVENT_TYPE]]</td>
</tr>
<tr>
<td>Number of Passengers:</td>
<td>[[NUM_PASSENGERS]]</td>
</tr>
<tr>
<td>Number of Hours:</td>
<td>[[NUM_HOURS]]</td>
</tr>
</table>
<hr>
Instructions:<br><br>[[INSTRUCTIONS]]
';

// Reservation fields.
$bookittrans_config['fields'] = array(
  array('key'=>'contact_name','name'=>'Contact Name','type'=>'text','class'=>'regular-text','default'=>'','placeholder'=>'What can we call you by?'),
  array('key'=>'contact_phone','name'=>'Contact Phone','type'=>'tel','class'=>'regular-text','default'=>''),
  array('key'=>'contact_email','name'=>'Contact Email','type'=>'email','class'=>'regular-text','default'=>''),
    array('key'=>'month','name'=>'Month','type'=>'select','default'=>'','options'=>array(
    '01'=>'01 - Jan',
    '02'=>'02 - Feb',
    '03'=>'03 - Mar',
    '04'=>'04 - Apr',
    '05'=>'05 - May',
    '06'=>'06 - June',
    '07'=>'07 - Jul',
    '08'=>'08 - Aug',
    '09'=>'09 - Sep',
    '10'=>'10 - Oct',
    '11'=>'11 - Nov',
    '12'=>'12 - Dec',
  )),
  array('key'=>'date','name'=>'Date','type'=>'select','default'=>'','options'=>$bookittrans_config['days']),
  array('key'=>'year','name'=>'Year','type'=>'select','default'=>'','options'=>$bookittrans_config['years']),
  array('key'=>'time','name'=>'Time','type'=>'select','default'=>'','options'=>$bookittrans_config['times']),
  array('key'=>'num_hours','name'=>'Number of Hours','type'=>'number','class'=>'small-text','default'=>'1'),
  array('key'=>'num_passengers','name'=>'Number of Passengers','type'=>'number','class'=>'small-text','default'=>'1'),
  array('key'=>'instructions','name'=>'Special Instructions','type'=>'textarea','class'=>'large-text','default'=>'1')
);

// Reservation categories
$bookittrans_config['categories'] = array(
  'vehicle' => array(
    'post_types'=>array('bookit_reservation'),
    'labels' => array(
      'name' => _x( 'Vehicles', 'taxonomy general name' ),
      'singular_name' => _x( 'Vehicle', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Vehicles' ),
      'all_items' => __( 'All Vehicles' ),
      'parent_item' => __( 'Parent Vehicle' ),
      'parent_item_colon' => __( 'Parent Vehicle:' ),
      'edit_item' => __( 'Edit Vehicle' ), 
      'update_item' => __( 'Update Vehicle' ),
      'add_new_item' => __( 'Add New Vehicle' ),
      'new_item' => __( 'New Vehicle Name' ),
      'menu_name' => __( 'Vehicles' )
     )
  ),
  'destinations' => array(
    'post_types'=>array('bookit_reservation'),
      'labels'=>array(
      'name' => _x( 'Destinations', 'taxonomy general name' ),
      'singular_name' => _x( 'Destination', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Destinations' ),
      'all_items' => __( 'All Destinations' ),
      'parent_item' => __( 'Parent Destination' ),
      'parent_item_colon' => __( 'Parent Destination:' ),
      'edit_item' => __( 'Edit Destination' ), 
      'update_item' => __( 'Update Destination' ),
      'add_new_item' => __( 'Add New Destination' ),
      'new_item' => __( 'New Destination Name' ),
      'menu_name' => __( 'Destinations' )
    )
  ),
  'pickup'=>array(
    'post_types'=>array('bookit_reservation'),
    'labels'=>array(
      'name' => _x( 'Pickup Locations', 'taxonomy general name' ),
      'singular_name' => _x( 'Pickup Location', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Pickup Locastions' ),
      'all_items' => __( 'All Pickup Locations' ),
      'parent_item' => __( 'Parent Pickup Locations' ),
      'parent_item_colon' => __( 'Parent Pickup Locations:' ),
      'edit_item' => __( 'Edit Pickup Location' ), 
      'update_item' => __( 'Update Pickup Location' ),
      'add_new_item' => __( 'Add New Pickup Location' ),
      'new_item' => __( 'New Pickup Name Name' ),
      'menu_name' => __( 'Pickup Locations' )
    )
  ),
  'event_type'=>array(
    'post_types'=>array('bookit_reservation'),
    'labels'=>array(
      'name' => _x( 'Event Types', 'taxonomy general name' ),
      'singular_name' => _x( 'Event Type', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Event Types' ),
      'all_items' => __( 'All Event Types' ),
      'parent_item' => __( 'Parent Event Type' ),
      'parent_item_colon' => __( 'Parent Event:' ),
      'edit_item' => __( 'Edit Event' ), 
      'update_item' => __( 'Update Event' ),
      'add_new_item' => __( 'Add New Event' ),
      'new_item' => __( 'New Evenet Name' ),
      'menu_name' => __( 'Events' )
    )
  ),
  'outsource_companies'=>array(
    'post_types'=>array('bookit_reservation'),
    'labels'=>array(
      'name' => _x( 'Outsource Companies', 'taxonomy general name' ),
      'singular_name' => _x( 'Outsource Company', 'taxonomy singular name' ),
      'search_items' =>  __( 'Search Outsource Companies' ),
      'all_items' => __( 'All Outsource Companies' ),
      'parent_item' => __( 'Parent Outsource Company' ),
      'parent_item_colon' => __( 'Parent Outsource Company:' ),
      'edit_item' => __( 'Edit Outsource Company' ), 
      'update_item' => __( 'Update Outsource Company' ),
      'add_new_item' => __( 'Add New Outsource Company' ),
      'new_item' => __( 'New Outsource Company' ),
      'menu_name' => __( 'Outsource Companies' )
    )
  )
);

// The plugin's custom post types
$bookittrans_config['post_types'] = array(
  'bookit_reservation' => array(
    'args' => array(
      'label' => 'Reservations',
      'labels' => array(
        'name' => 'Reservations',
        'singular_name' => 'Reservation',
        'add_new' => 'Add New Reservation',
        'add_new_item' => 'Add New Reservation',
        'edit_item' => 'Edit Reservation',
        'new_item' => 'New Reservation',
        'all_items' => 'All Reservations',
        'view_item' => 'View Reservation',
        'search_items' => 'Search Reservations',
        'not_found' =>  'No reservations found',
        'not_found_in_trash' => 'No reservations found in Trash', 
        'parent_item_colon' => '',
        'menu_name' => 'Book It! Transportation'
      ),
      'description' => 'Reservation booking for the Book It! Transportation plugin.',
      'public' => false,
      'publicly_queryable' => false,
      'show_ui' => true,
      'show_in_menu' => true,
      'show_in_admin_bar' => false,
      'query_var' => false,
      'rewrite' => array( 'slug' => 'reservation' ),
      'exclude_from_search' => false,
      'capability_type' => 'post',
      'has_archive' => false, 
      'hierarchical' => false,
      'menu_position' => null,
      'supports' => array(
        'title',
        'author',
        'revisions'
      )
    )
  )
);


// Set the required fields for booking a reservation
$bookittrans_config['required_fields'] = array('contact_name','contact_phone','contact_email','month','date','year','time','num_passengers','pickup','vehicle','event_type');

// Default reservation status
$bookittrans_config['reservation-status'] ='pending-review';

// Reservation recieved page
$bookittrans_config['reservation-received-url'] = get_option('bookittrans_reservation_received_url');
