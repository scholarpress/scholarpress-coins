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
    if ( empty( $metabox_display_data ) || ! is_array( $metabox_display_data ) ) {
        echo 'Sorry, something went wrong with the ScholarPress COinS plugin!';
        return;
    }

    ?>

    <label for="coins-title"><?php esc_html_e( 'Title:', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-title" name="_coins-title" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-title'] ) ?>">

    <label for="coins-source"><?php esc_html_e( 'Source:', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-source" name="_coins-source" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-source'] ) ?>">

    <label for="coins-date"><?php esc_html_e( 'Date:', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-date" name="_coins-date" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-date'] ) ?>">

    <label for="coins-identifier"><?php esc_html_e( 'Identifier:', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-identifier" name="_coins-identifier" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-identifier'] ) ?>">

    <label for="coins-author-first"><?php esc_html_e( 'Author/Creator\'s first or given name:', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-author-first" name="_coins-author-first" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-author-first'] ) ?>">

    <label for="coins-author-last"><?php esc_html_e( 'Author/Creator\'s last or family name:', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-author-last" name="_coins-author-last" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-author-last'] ) ?>">

    <?php
    if ( ! empty( $metabox_display_data['_coins-subjects'] ) && is_array( $metabox_display_data['_coins-subjects'] ) ) {
        $metabox_display_data['_coins-subjects'] = implode( ', ', $metabox_display_data['_coins-subjects'] );
    } else {
        $metabox_display_data['_coins-subjects'] = '';
    }
    ?>

    <label for="coins-subjects"><?php esc_html_e( 'Comma-separated list of subjects', 'scholarpress-coins' ); ?></label>
    <input class="widefat" id="coins-subjects" name="_coins-subjects" type="text" value="<?php echo esc_attr( $metabox_display_data['_coins-subjects'] ) ?>">

    <?php
}

add_action( 'save_post', 'scholarpress_coins_save_metadata' );
function scholarpress_coins_save_metadata( $post_id ) {
    $legacy_data = scholarpress_coins_get_legacy_post_data( $post_id );
    foreach( scholarpress_coins_keys() as $key ) {
        $currently_saved_value = get_post_meta( $post_id, $key );
        if ( array_key_exists( $key, $_POST) ) {
            if ( $_POST[$key] == '' ) {
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
    $content = '<span class="Z3988" title="' . esc_html( $coinsTitle ) . '"></span>' . $content;
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
        $coinsTitle .=  '&amp;rft.aulast='.urlencode( $data['_coins-author-last'] )
                        . '&amp;rft.aufirst='.urlencode( $data['_coins-author-first'] );
    } else {
        $coinsTitle .= '&amp;rft.au='.urlencode( $data['_coins-author-first'] );
    }

    return $coinsTitle;
}

function scholarpress_coins_keys() {
    return array(
        '_coins-title',
        '_coins-source',
        '_coins-date',
        '_coins-identifier',
        '_coins-author-first',
        '_coins-author-last',
        '_coins-subjects'
    );
}
function scholarpress_coins_prepare_data_for_display( $post_id ) {
    if( empty( $post_id ) )
        return false;

    $legacy_data = scholarpress_coins_get_legacy_post_data();
    $return_data = array();

    foreach( scholarpress_coins_keys() as $key ) {
        $value_from_db = $return_data[$key] = get_post_meta( $post_id, $key, true );
        if( empty( $value_from_db ) ) {
            if ( ! empty( $legacy_data[$key] ) && $legacy_data[$key] != 'scholarpress_coins_empty' ) {
                $return_data[$key] = $legacy_data[$key];
            } else {
                $return_data[$key] = '';
            }
        } else {
            if ( $value_from_db == 'scholarpress_coins_empty' ) {
                $return_data[$key] = '';
            } else {
                $return_data[$key] = $value_from_db;
            }
        }
    }

    return $return_data;
}
function scholarpress_coins_get_legacy_post_data( $post_id = null ) {
    global $post, $authordata;
    if ( ! $post ) {
        $post = get_post( $post_id );
        if ( empty( $post ) ) {
            return false;
        }
    }
    if ( ! $authordata ) {
        $authordata = get_userdata( $post->post_author );
    }
    $legacy_coins_data = array();

    $authorLast = $authordata->last_name;
    $authorFirst = $authordata->first_name;
    if ( ! empty( $authorLast ) &&  ! empty( $authorFirst ) ) {
        $legacy_coins_data['_coins-author-first'] = $authorFirst;
        $legacy_coins_data['_coins-author-last'] = $authorLast;
    } else {
        $legacy_coins_data['_coins-author-first'] = $authordata->display_name;
        $legacy_coins_data['_coins-author-last'] = 'scholarpress_coins_empty';
    }

    $legacy_coins_data['_coins-title'] = $post->post_title;
    $legacy_coins_data['_coins-source'] = get_bloginfo( 'name' );
    $legacy_coins_data['_coins-date'] = get_the_time( 'Y-m-d' );
    $legacy_coins_data['_coins-identifier'] = get_permalink( $post->ID );

    if ( $cats = get_the_category() ) {
        $legacy_coins_data['_coins-subjects'] = array();
        foreach( $cats as $cat ) {
            $legacy_coins_data['_coins-subjects'][] = $cat->cat_name;
        }
    }

    return $legacy_coins_data;
}

?>
