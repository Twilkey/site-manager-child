<?php
function get_key_check( $secret ) {
    if(!defined('SECRET_KEY')){
        $csm_secret_key = csm_get_option( 'csm_secret_key' );
        define( 'SECRET_KEY', $csm_secret_key );
    }

    if($secret == SECRET_KEY) {
        return true;
    }
    return false;
}

function csm_get_option( $key, $plugin = "" ){
    if($plugin != "") {
        if(is_plugin_active($plugin)) {
            return get_option( $key );
        }
        return false;
    }
    return get_option( $key );
}