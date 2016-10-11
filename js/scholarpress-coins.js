jQuery( document ).ready( function() {
	var coins_metabox = document.getElementById( 'scholarpress-coins-meta-box' );

	var fields_to_lock = ['title', 'author-first', 'subjects', 'identifier'];
	fields_to_lock.forEach( function( field ) {
		var field_lock_id = 'coins-' + field + '-lock';
		document.getElementById( field_lock_id ).addEventListener( 'click', function( e ) {
			var field_id = e.target.id.replace('-lock', '');
			var field = document.getElementById( field_id );
			if ( this.checked ) {
				field.disabled = true;
				field.value = document.getElementsByClassName(field_id + '-hidden')[0].value;
				if ( field_id == 'coins-author-first' ) {
					var last_name_field = document.getElementById( 'coins-author-last' )
					last_name_field.disabled = true;
					last_name_field.value = document.getElementsByClassName( 'coins-author-last-hidden' )[0].value;
				}
			} else {
				field.disabled = false;
				if ( field_id == 'coins-author-first' ) {
					document.getElementById( 'coins-author-last' ).disabled = false;
				}
			}

		} );
	} );
});