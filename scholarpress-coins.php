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

$coins_postmeta_keys = array(
    '_coins-title',
    '_coins-source',
    '_coins-date',
    '_coins-identifier',
    '_coins-author-first',
    '_coins-author-last',
    '_coins-subjects'
);

add_action( 'add_meta_boxes', 'scholarpress_coins_add_coins_meta_box' );

function scholarpress_coins_add_coins_meta_box() {
    global $post;
    $authordata = get_userdata( $post->post_author );

    $default_coins_data = array();

    $authorLast = $authordata->last_name;
    $authorFirst = $authordata->first_name;
    if ( ! empty( $authorLast ) &&  ! empty( $authorFirst ) ) {
        $default_coins_data['_coins-author-last'] = $authorLast;
        $default_coins_data['_coins-author-first'] = $authorFirst;
    } else {
        $default_coins_data['_coins-author-first'] = $authordata->display_name;
        $default_coins_data['_coins-author-last'] = 'scholarpress_coins_empty';
    }

    $default_coins_data['_coins-subjects'] = array();
    if ( $cats = get_the_category() ) {
        foreach( $cats as $cat ) {
            $default_coins_data['_coins-subjects'][] = $cat->cat_name;
        }
    }

    $default_coins_data['_coins-title'] = $post->post_title;
    $default_coins_data['_coins-source'] = get_bloginfo( 'name' );
    $default_coins_data['_coins-date'] = get_the_time( 'Y-m-d' );
    $default_coins_data['_coins-identifier'] = get_permalink( $post->ID );

    add_meta_box(
        'scholarpress-coins-meta-box',
        'Bibliographic Information',
        'scholarpress_coins_show_meta_box',
        array( 'post', 'page' ),
        'side',
        'default',
        $default_coins_data
    );
}

function scholarpress_coins_show_meta_box( $post, $meta_box_args ) {
    global $coins_postmeta_keys;
    $default_coins_data = $meta_box_args['args'];
    $coins_display_data = array();

    foreach( $coins_postmeta_keys as $coins_postmeta_key ) {
        if ( $coins_postmeta_key == '_coins-subjects' ) {
            $saved_coins_subjects =  get_post_meta( $post->ID, '_coins-subjects', true );

            if ( !empty( $saved_coins_subjects ) && $saved_coins_subjects != 'scholarpress_coins_empty' ) {
                $coins_display_data['_coins-subjects']  = implode( ', ', $saved_coins_subjects );
            } elseif ( !empty( $default_coins_data['_coins-subjects'] ) ) {
                    $coins_display_data['_coins-subjects']  = implode( ', ', $default_coins_data['_coins-subjects'] );
            } else {
                $coins_display_data['_coins-subjects'] = '';
            }

        } else {
            $coins_postmeta_value = get_post_meta( $post->ID, $coins_postmeta_key, true );
            
            if ( !empty( $coins_postmeta_value ) ) {
                if ( $coins_postmeta_value == 'scholarpress_coins_empty' ) {
                    $coins_display_data[$coins_postmeta_key] = '';
                } else {
                    $coins_display_data[$coins_postmeta_key] = $coins_postmeta_value;
                }
            } else {
                if ( $default_coins_data[$coins_postmeta_key] == 'scholarpress_coins_empty' ) {
                    $coins_display_data[$coins_postmeta_key] = '';
                } else {
                    $coins_display_data[$coins_postmeta_key] = $default_coins_data[$coins_postmeta_key];
                }
            }
        }
    }

    echo '<label for="coins-title">Title: </label><input class="widefat" id="coins-title" name="_coins-title" type="text" value="' . $coins_display_data['_coins-title'] . '">';
    echo '<label for="coins-source">Source: </label><input class="widefat" id="coins-source" name="_coins-source" type="text" value="' . $coins_display_data['_coins-source'] . '">';
    echo '<label for="coins-date">Date: </label><input class="widefat" id="coins-date" name="_coins-date" type="text" value="' . $coins_display_data['_coins-date'] . '">';
    echo '<label for="coins-identifier">Identifier: </label><input class="widefat" id="coins-identifier" name="_coins-identifier" type="text" value="' . $coins_display_data['_coins-identifier'] . '">';
    echo '<label for="coins-author-first">Author/Creator\'s first or given name: </label><input class="widefat" id="coins-author-first" name="_coins-author-first" type="text" value="' . $coins_display_data['_coins-author-first'] . '">';
    echo '<label for="coins-author-first">Author/Creator\'s last or family name (if applicable): </label><input class="widefat" id="coins-author-last" name="_coins-author-last" type="text" value="' . $coins_display_data['_coins-author-last'] . '">';
    echo '<label for="coins-subjects">Comma-sparated list of subjects: </label><input class="widefat" id="coins-subjects" name="_coins-subjects" type="text" value="' . $coins_display_data['_coins-subjects'] . '">';
}


add_action( 'save_post', 'scholarpress_coins_save_metadata' );

function scholarpress_coins_save_metadata( $post_id ) {
    global $coins_postmeta_keys;
    foreach( $_POST as $key => $value ) {
        if ( in_array( $key, $coins_postmeta_keys) ) {
            if ( $_POST[$key] == '' ) {
                update_post_meta( $post_id, $key, 'scholarpress_coins_empty' );
            } elseif ( $key == '_coins-subjects' ) {
                $coins_subjects_string = str_replace( ', ', ',', $value );
                $coins_subjects_array = explode( ',', $coins_subjects_string );
                update_post_meta( $post_id, '_coins-subjects', $coins_subjects_array );
            } else {
                update_post_meta( $post_id, $key, $_POST[$key] );
            }
        }
    }
}

add_filter('the_content', 'scholarpress_coins_add_coins_metadata');

function scholarpress_coins_add_coins_metadata($content)
{
    global $post, $authordata, $coins_postmeta_keys;

    $coins_display_postmeta = array();
    foreach( $coins_postmeta_keys as $key ) {
        $coins_display_postmeta[$key] = get_post_meta( $post->ID, $key, true );
        if ( $coins_display_postmeta[$key] == 'scholarpress_coins_empty' ) {
            $coins_display_postmeta[$key] = '';
        }
    }

    $coinsTitle = 'ctx_ver=Z39.88-2004'
                . '&amp;rft_val_fmt=info%3Aofi%2Ffmt%3Akev%3Amtx%3Adc'
                . '&amp;rfr_id=info%3Asid%2Focoins.info%3Agenerator'
                . '&amp;rft.type='
                . '&amp;rft.format=text'
                . '&amp;rft.title='. urlencode( $coins_display_postmeta['_coins-title'] )
                . '&amp;rft.source='. urlencode( $coins_display_postmeta['_coins-source'] )
                . '&amp;rft.date='. $coins_display_postmeta['_coins-date']
                . '&amp;rft.identifier='. urlencode( $coins_display_postmeta['_coins-identifier'] )
                . '&amp;rft.language=English';

    if ( !empty( $coins_display_postmeta['_coins-subjects'] ) && is_array( $coins_display_postmeta['_coins-subjects'] ) ) {
        foreach( $coins_display_postmeta['_coins-subjects'] as $subject ) {
            $coinsTitle .= '&amp;rft.subject='.urlencode( $subject );
        }
    }

    if ( !empty( $coins_display_postmeta['_coins-author-last'] ) && !empty( $coins_display_postmeta['_coins-author-first'] ) ) {
        $coinsTitle = $coinsTitle
                    . '&amp;rft.aulast='.urlencode( $coins_display_postmeta['_coins-author-last'] )
                    . '&amp;rft.aufirst='.urlencode( $coins_display_postmeta['_coins-author-first'] );
    } else {
        $coinsTitle = $coinsTitle
                    . '&amp;rft.au='.urlencode( $coins_display_postmeta['_coins-author-first'] );
    }

    $coinsTitle = apply_filters('scholarpress_coins_span_title', $coinsTitle);

    $content = '<span class="Z3988" title="'.$coinsTitle.'"></span>' . $content;

    return $content;
}
?>
