jQuery(function() {
    var availableTags = [];
    jQuery.each(bookit, function(index, value) {
      availableTags.push(value.name);
    });
    jQuery( ".bookit-destinations" )
      // don't navigate away from the field on tab when selecting an item
      .bind( "keydown", function( event ) {
        if ( event.keyCode === jQuery.ui.keyCode.TAB &&
            jQuery( this ).data( "autocomplete" ).menu.active ) {
          event.preventDefault();
        }
      })
      .autocomplete({
        minLength: 0,
        source: availableTags,
        focus: function() {
          // prevent value inserted on focus
          return false;
        },
        select: function( event, ui ) {
          this.value = ui.item.value;
          return false;
        }
      });
  });