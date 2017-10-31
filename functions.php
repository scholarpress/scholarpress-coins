<?php

function scholarpress_coins__enqueue_assets() {
    wp_enqueue_script( 'scholarpress-coins', plugins_url( '/js/scholarpress-coins.js', __FILE__ ), array(), '2.1', true );
    wp_enqueue_style( 'scholarpress-coins', plugins_url( 'scholarpress-coins.css', __FILE__ ) );
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

function scholarpress_coins_get_locked_fields( $post_id = null ) {
    global $post;
    if ( ! $post ) {
        $post = get_post( $post_id );
        if ( empty( $post ) ) {
            return false;
        }
    }
    $locked_fields = array();
    foreach( scholarpress_coins_keys() as $key ) {
        if ( get_post_meta( $post->ID, $key . '-lock', true ) ) {
            $locked_fields[] = $key;
        }
    }
    return $locked_fields;
}

?>