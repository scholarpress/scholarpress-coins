<?php
/*
Plugin Name: ScholarPress Coins
Plugin URI: http://www.scholarpress.net/coins/
Description: Makes your blog posts readable by various COinS interpreters.
Version: 2.0
Author: Sean Takats, Jeremy Boggs, Daniel Jones, Boone Gorges
Author URI: http://chnm.gmu.edu

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

add_action( 'init', 'scholarpress_coins_init' );
function scholarpress_coins_init() {
    include_once( 'functions.php' );
}

add_action( 'add_meta_boxes', 'scholarpress_coins_add_coins_meta_box' );
function scholarpress_coins_add_coins_meta_box() {
    add_meta_box(
        'scholarpress-coins-meta-box',
        'Bibliographic Information',
        'scholarpress_coins_show_meta_box',
        array( 'post', 'page' ),
        'side',
        'default'
    );
}
function scholarpress_coins_show_meta_box( $post ) {
    $metabox_display_data = scholarpress_coins_prepare_data_for_display( $post->ID );
    $locked_fields = scholarpress_coins_get_locked_fields();
    if ( empty( $metabox_display_data ) || ! is_array( $metabox_display_data ) ) {
        echo __( 'Sorry, something went wrong with the ScholarPress COinS plugin!', 'scholarpress-coins' );
        return;
    }

    // Post Title field
    echo '<label for="coins-title">Title: </label><input class="widefat" id="coins-title" name="_coins-title" type="text" value="' . esc_html( $metabox_display_data['_coins-title'] ) . '"'; 
    if ( in_array( '_coins-title', $locked_fields ) ) {
        echo ' disabled';
    }
    echo '>';
    echo '<input id="coins-title-lock" name="_coins-title-lock" type="checkbox"';
    if ( in_array( '_coins-title', $locked_fields ) ) {
        echo ' checked';
    }
    echo '>';
    echo '<label for="coins-title-lock">Lock field to post title?</label></br></br>';

    // Author Name fields
    echo '<label for="coins-author-first">Author/Creator\'s first or given name: </label><input class="widefat" id="coins-author-first" name="_coins-author-first" type="text" value="' . esc_html( $metabox_display_data['_coins-author-first'] ) . '"';
    if ( in_array( '_coins-author-first', $locked_fields ) ) {
        echo ' disabled';
    }
    echo '>';
    echo '<label for="coins-author-last">Author/Creator\'s last or family name (if applicable): </label><input class="widefat" id="coins-author-last" name="_coins-author-last" type="text" value="' . esc_html( $metabox_display_data['_coins-author-last'] ) . '"';
    if ( in_array( '_coins-author-first', $locked_fields ) ) {
        echo ' disabled';
    }
    echo '>';
    echo '<input id="coins-author-first-lock" name="_coins-author-first-lock" type="checkbox"';
    if ( in_array( '_coins-author-first', $locked_fields ) ) {
        echo ' checked';
    }
    echo '>';
    echo '<label for="coins-author-first-lock">Lock fields to post author?</label></br></br>';

    echo '<label for="coins-source">Source: </label><input class="widefat" id="coins-source" name="_coins-source" type="text" value="' . esc_html( $metabox_display_data['_coins-source'] ) . '">';
    echo '<label for="coins-date">Date: </label><input class="widefat" id="coins-date" name="_coins-date" type="text" value="' . esc_html( $metabox_display_data['_coins-date'] ) . '">';
    echo '<label for="coins-identifier">Identifier: </label><input class="widefat" id="coins-identifier" name="_coins-identifier" type="text" value="' . esc_html( $metabox_display_data['_coins-identifier'] ) . '">';

    // Subjects field
    if ( ! empty( $metabox_display_data['_coins-subjects'] ) && is_array( $metabox_display_data['_coins-subjects'] ) ) {
        $metabox_display_data['_coins-subjects'] = implode( ', ', $metabox_display_data['_coins-subjects'] );
    } else {
        $metabox_display_data['_coins-subjects'] = '';
    }
    echo '<label for="coins-subjects">Comma-sparated list of subjects: </label>
    <input class="widefat" id="coins-subjects" name="_coins-subjects" type="text" value="' . esc_html( $metabox_display_data['_coins-subjects'] ) . '"';
    if ( in_array( '_coins-subjects', $locked_fields ) ) {
        echo ' disabled';
    }
    echo '>';
    echo '<input id="coins-subjects-lock" name="_coins-subjects-lock" type="checkbox"';
    if ( in_array( '_coins-subjects', $locked_fields ) ) {
        echo ' checked';
    }
    echo '>';
    echo '<label for="coins-subjects-lock">Lock field to post categories?</label></br></br>';

}

add_action( 'save_post', 'scholarpress_coins_save_metadata' );
function scholarpress_coins_save_metadata( $post_id ) {
    $legacy_data = scholarpress_coins_get_legacy_post_data( $post_id );
    foreach( scholarpress_coins_keys() as $key ) {
        if ( array_key_exists( $key . '-lock', $_POST ) && 'on' === $_POST[$key . '-lock'] ) {
            update_post_meta( $post_id, $key . '-lock', true );
            if ( $key == '_coins-title' ) {
                update_post_meta( $post_id, $key, $_POST['post_title'] );
            }
            elseif ( $key == '_coins-author-first' ) {
                $author = get_userdata( $_POST['post_author'] );
                $authorLast = $author->last_name;
                $authorFirst = $author->first_name;
                if ( ! empty( $authorLast ) &&  ! empty( $authorFirst ) ) {
                    update_post_meta( $post_id, '_coins-author-first', $authorFirst );
                    update_post_meta( $post_id, '_coins-author-last', $authorLast );
                } else {
                    update_post_meta( $post_id, '_coins-author-first', $author->display_name );
                    update_post_meta( $post_id, '_coins-author-last', 'scholarpress_coins_empty' );
                    unset( $_POST['_coins-author-last'] );
                }
            } elseif ( $key == '_coins-subjects' ) {
                $cats = $_POST['post_category'];
                $cat_names = array();
                foreach( $cats as $index => $cat ) {
                    $cat_obj = get_category( $cat );
                    if ( ! is_wp_error( $cat_obj ) ) {
                        $cat_names[] = $cat_obj->name;
                    }
                }
                update_post_meta( $post_id, '_coins-subjects', $cat_names );
            }  
        } elseif ( array_key_exists( $key, $_POST ) ) {
            if ( array_key_exists( $key . '-lock', $_POST ) && 'on' === $_POST[$key . '-lock'] ) {

            } elseif ( $_POST[$key] == '' ) {
                if( empty( $currently_saved_value ) && ! empty( $legacy_data[$key] ) && $legacy_data[$key] != 'scholarpress_coins_empty' ) {
                    $new_value = $legacy_data[$key];
                } else {
                    $new_value = 'scholarpress_coins_empty';
                }
                update_post_meta( $post_id, $key, $new_value );
            } elseif ( $key == '_coins-subjects' ) {
                $coins_subjects_string = str_replace( ', ', ',', $_POST['_coins-subjects'] );
                $coins_subjects_array = explode( ',', $coins_subjects_string );
                update_post_meta( $post_id, '_coins-subjects', $coins_subjects_array );
            } else {
                update_post_meta( $post_id, $key, wp_unslash( $_POST[$key] ) );
            }
        }
    }
}

add_filter( 'the_content', 'scholarpress_coins_add_coins_to_content' );
function scholarpress_coins_add_coins_to_content( $content ) {
    global $post;
    $display_data = scholarpress_coins_prepare_data_for_display( $post->ID );
    if ( empty( $display_data ) ) {
        return $content;
    }
    $coinsTitle = apply_filters('scholarpress_coins_span_title', scholarpress_coins_get_span_title( $display_data ) );
    $content = '<span class="Z3988" title="'. $coinsTitle .'"></span>' . $content;
    return $content;
}
function scholarpress_coins_get_span_title( $data ) {
    if ( empty( $data ) || ! is_array( $data ) )
        return '';

    $coinsTitle = 'ctx_ver=Z39.88-2004'
                . '&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc'
                . '&amp;rfr_id=info%3Asid%2Focoins.info%3Agenerator'
                . '&amp;rft.type='
                . '&amp;rft.format=text';
    if ( ! empty( $data['_coins-title'] ) )
        $coinsTitle .= '&amp;rft.title='. urlencode( $data['_coins-title'] );
    if ( ! empty( $data['_coins-source'] ) )
        $coinsTitle .= '&amp;rft.source='. urlencode( $data['_coins-source'] );
    if ( ! empty( $data['_coins-date'] ) )
        $coinsTitle .= '&amp;rft.date='. $data['_coins-date'];
    if ( ! empty( $data['_coins-identifier'] ) )
        $coinsTitle .= '&amp;rft.identifier='. urlencode( $data['_coins-identifier'] );
    $coinsTitle .= '&amp;rft.language=English';

    if ( ! empty( $data['_coins-subjects'] ) && is_array( $data['_coins-subjects'] ) ) {
        foreach( $data['_coins-subjects'] as $subject ) {
            $coinsTitle .= '&amp;rft.subject=' . urlencode( $subject );
        }
    }
    if ( ! empty( $data['_coins-author-last'] ) && ! empty( $data['_coins-author-first'] ) ) {
        $coinsTitle .=  '&amp;rft.aulast=' . urlencode( $data['_coins-author-last'] )
                        . '&amp;rft.aufirst=' . urlencode( $data['_coins-author-first'] );
    } else {
        $coinsTitle .= '&amp;rft.au=' . urlencode( $data['_coins-author-first'] );
    }

    return $coinsTitle;
}

function scholarpress_coins_enqueue_scripts() {
    wp_enqueue_script( 'scholarpress-coins', plugins_url( '/js/scholarpress-coins.js', __FILE__ ), array(), '2.0', true );
}
add_action( 'admin_enqueue_scripts', 'scholarpress_coins_enqueue_scripts' );
?>