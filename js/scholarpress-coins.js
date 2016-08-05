jQuery( document ).ready( function() {
	var coins_metabox = document.getElementById( 'scholarpress-coins-meta-box' );
	
	var title_field_lock = document.getElementById( 'coins-title-lock' );
	var author_field_lock = document.getElementById( 'coins-author-first-lock' );
	var subjects_field_lock = document.getElementById( 'coins-subjects-lock' );

	var field_locks = [title_field_lock, author_field_lock, subjects_field_lock];
	field_locks.forEach( function( element ) {
		element.addEventListener( 'click', function( e ) {
			var field_id = e.target.id.replace('-lock', '');
			var field = document.getElementById( field_id );
			if ( this.checked ) {
				field.disabled = true;
			} else {
				field.disabled = false;
			}
		});
	});
});