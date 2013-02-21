<?php
if( $bookit_config['premium'] ) {
  /*add_action( 'post_submitbox_misc_actions', 'bookit_publish_box' );
  function bookit_publish_box() {
    global $post;
    if (get_post_type($post) == 'bookit_reservation') {
    ?>
    <div class="misc-pub-section misc-pub-section">
      <h4><?php echo __('The Money Box', 'bookit')?></h4>
      <label for="bookit_quoted_price"><?php echo __('Quoted Price:', 'bookit')?></label>
      <input type="number" name="bookit_quoted_price" id="bookit_quoted_price" step=".1" min="0" value="<?php echo get_post_meta( $post->ID, 'bookit_quoted_price', true )?>">
    </div>
    <?
    }
  }*/

  add_action( 'admin_init', 'bookit_admin_premium' );
  function bookit_admin_premium() {
    global $pagenow;
    add_meta_box( 'reservation_details', 'Reservation Details', 'display_resevation_details', 'bookit_reservation', 'normal', 'core' );
    if( $pagenow == 'post.php' ) {
      add_meta_box( 'reservation_notification_options', 'Notification Options', 'display_notification_options', 'bookit_reservation', 'side', 'core' );
    }
  }
}