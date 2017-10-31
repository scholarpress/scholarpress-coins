jQuery(document).ready(function() {
	var coins_metabox = document.getElementById('scholarpress-coins-meta-box');
	if (coins_metabox === null) {
		return false;
	}
	var add_author_button = document.getElementById('coins-add-author');
	add_author_button.addEventListener('click', coins_add_author);

	var author_fieldsets = document.getElementsByClassName( "coins_single_author" );
	for ( var i = 1; i < author_fieldsets.length; i++ ) {
		add_remove_button( author_fieldsets[i] );
	}

	var locking_field_names = ['title', 'author', 'subjects', 'identifier'];
	locking_field_names.forEach( function(field) {
		var locking_field_checkbox = document.getElementById('coins-' + field + '-lock');
		if(!locking_field_checkbox) {
			return;
		}
		locking_field_checkbox.addEventListener('click', function(e) {
			var locking_field_id = e.target.id.replace('-lock', '');
			var locking_field = document.getElementById( locking_field_id );

			if (this.checked) {
				locking_field.disabled = true;
				if ( locking_field_id == 'coins-author' ) {
					for (var i = 1; i < author_fieldsets.length; i++) {
						author_fieldsets[i].remove();
					}
					document.getElementById('coins-add-author').disabled = true;
					var first_name_field = document.getElementById('coins-author-first-primary');
					var last_name_field = document.getElementById('coins-author-last-primary');
					first_name_field.value = document.getElementsByClassName( 'coins-author-first-hidden' )[0].value;
					last_name_field.value = document.getElementsByClassName( 'coins-author-last-hidden' )[0].value;
				} else {
					locking_field.value = document.getElementsByClassName(locking_field_id + '-hidden')[0].value;
				}
			} else {
				locking_field.disabled = false;
				if (locking_field_id == 'coins-author') {
					document.getElementById('coins-add-author').disabled = false;
				}
			}

		} );
	} );


});

function coins_add_author( event ) {
	event.preventDefault();
	add_author_fieldset();
}

function add_author_fieldset() {
	var new_fieldset = document.createElement("fieldset");

	// First Name label and field
	var first_name_label = document.createElement("label");
	var first_name_label_text = document.createTextNode("First name:");
	first_name_label.appendChild(first_name_label_text);

	var first_name_field = document.createElement("input");
	first_name_field.setAttribute("name", "_coins-author-first[]");
	first_name_field.setAttribute("type", "text");
	first_name_field.classList.add("widefat");
	first_name_label.appendChild(first_name_field);

	new_fieldset.appendChild(first_name_label);

	// Last Name label and field
	var last_name_label = document.createElement("label");
	var last_name_label_text = document.createTextNode("Last name:");
	last_name_label.appendChild(last_name_label_text);

	var last_name_field = document.createElement("input");
	last_name_field.setAttribute("name", "_coins-author-last[]");
	last_name_field.setAttribute("type", "text");
	last_name_field.classList.add("widefat");
	last_name_label.appendChild(last_name_field);

	new_fieldset.appendChild(last_name_label);

	// Remove button
	var remove_button = document.createElement("button");
	remove_button.classList.add("remove_author_button");
	var remove_button_text = document.createTextNode('Remove author');
	remove_button.appendChild(remove_button_text);
	remove_button.addEventListener('click', remove_button_parent);
	new_fieldset.appendChild(remove_button);

	var author_fieldset = document.getElementById('coins-author');
	new_fieldset.classList.add("coins_single_author");
	author_fieldset.appendChild(new_fieldset);
}

function add_remove_button( author_fieldset ) {
	console.log( author_fieldset );

	var remove_button = document.createElement("button");
	remove_button.classList.add("remove_author_button");
	var remove_button_text = document.createTextNode('Remove author');
	remove_button.appendChild(remove_button_text);
	remove_button.addEventListener('click', remove_button_parent);
	author_fieldset.appendChild(remove_button);
}

function remove_button_parent( event ) {
	event.preventDefault();
	this.parentNode.remove();
}