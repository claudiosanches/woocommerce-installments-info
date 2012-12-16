<?php

// If uninstall not called from WordPress exit.
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) || !WP_UNINSTALL_PLUGIN || dirname( WP_UNINSTALL_PLUGIN ) != dirname( plugin_basename( __FILE__ ) ) ) {

    status_header( 404 );
    exit;
}

delete_option( 'wcccit_settings' );
delete_option( 'wcccit_design' );
delete_option( 'wcccit_icons' );
