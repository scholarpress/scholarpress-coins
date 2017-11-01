<?php
function scholarpress_coins__author_fields( $data, $locked ) {
    $authors = array();
    $legacy_author = array();
    if ( ! is_array( $data['_coins-author-first'] ) ) {
        $legacy_author['first'] = $data['_coins-author-first'];
        if ( $data['_coins-author-last'] )
            $legacy_author['last'] = $data['_coins-author-last'];
        array_push($authors, $legacy_author);
    } else {
        foreach( $data['_coins-author-first'] as $index => $author_first ) {
            array_push($authors,
                array(
                    'first' => $author_first,
                    'last' => $data['_coins-author-last'][$index],
                )
            );
        }
    }
	?>
    <div class="scholarpress-field__input">
	<fieldset <?php if ( $locked ) echo 'disabled'; ?> id="coins-author">
		<legend><?php esc_html_e( 'Authors', 'scholarpress-coins' ); ?></legend>
		<?php
	    foreach ( $authors as $index => $author ) {
	    	scholarpress_coins__single_author_fieldset( $author, $index );
	    }
	    ?>
    </fieldset>

    <button id="coins-add-author" class="add_author_button" <?php if ( $locked ) echo 'disabled'; ?>><?php esc_html_e( 'Add author', 'scholarpress-coins' ); ?></button>
    </div>

    <div class="scholarpress-field__lock">
        <input
            id="coins-author-lock"
            name="_coins-author-lock"
            type="checkbox"
            <?php if ( $locked ) echo ' checked'; ?>
        >
        <label for="coins-author-lock">
            <?php esc_html_e( 'Lock fields to post author?', 'scholarpress-coins' ); ?>
        </label>
    </div>
    <?php
    global $post;
    $default_author_data = get_userdata( $post->post_author );
    $default_author_first = $default_author_data->first_name;
    $default_author_last = $default_author_data->last_name;

    if ( ! ($default_author_last && $default_author_first ) ) {
        $default_author_first = $default_author_data->display_name;
        $default_author_last = '';
    }
    ?>
    <input type="hidden" class="coins-author-first-hidden" value="<?php echo esc_attr( $default_author_first ); ?>">
    <input type="hidden" class="coins-author-last-hidden" value="<?php echo esc_attr( $default_author_last ); ?>">

    <?php
}

function scholarpress_coins__single_author_fieldset( $author, $field_id_index ) {
?>
    <fieldset class="coins_single_author">
    	<label>
			<?php esc_html_e( 'First name:', 'scholarpress-coins' ); ?>
        	<input
        		class="widefat"
        		<?php if ( $field_id_index === 0 ) : ?>
                    id="coins-author-first-primary"
        		<?php endif; ?>
                name="_coins-author-first[]"
        		type="text"
        		value="<?php echo esc_attr( $author['first'] ); ?>"
        	>
        </label>
       	<label>
			<?php esc_html_e( 'Last name:', 'scholarpress-coins' ); ?>
            <input
                class="widefat"
                <?php if ( $field_id_index === 0 ) : ?>
                    id="coins-author-last-primary"
                <?php endif; ?>
                name="_coins-author-last[]"
                type="text"
                <?php if ( isset( $author['last'] ) ) : ?>
                    value="<?php echo esc_attr( $author['last'] ); ?>"
                <?php endif; ?>
            >
    	</label>
    </fieldset>
<?php
}

function scholarpress_coins__input_field( $field, $title, $value, $locked ) {
?>
    <div class="scholarpress-field__input">
        <label for="<?php echo esc_attr( 'coins-' . $field ); ?>">
            <?php echo esc_attr( $title ); ?></label>
        <input
            class="widefat"
            id="<?php echo esc_attr( 'coins-' . $field ); ?>"
            name="<?php echo esc_attr( '_coins-' . $field ); ?>"
            type="text"
            value="<?php echo esc_attr( $value ); ?>"
            <?php if ( $locked ) echo ' disabled'; ?>
        >
        </input>
    </div>
<?php
}

function scholarpress_coins__field_lock( $field, $locked, $label_text ) {
?>
    <div class="scholarpress-field__lock">
        <input
            id="coins-<?php echo esc_attr( $field ); ?>-lock"
            name="_coins-<?php echo esc_attr( $field ); ?>-lock"
            type="checkbox"'
            <?php if ( $locked ) echo ' checked'; ?>
        >
        <label for="coins-<?php echo esc_attr( $field ); ?>-lock"><?php echo esc_html( $label_text ); ?></label>
    </div>
<?php
}
