<?php
/**
 * Plugin Name: WooCommerce Installments Info
 * Plugin URI: http://www.claudiosmweb.com/
 * Description: Displays Installments Info in you WooCommerce
 * Author: claudiosanches, rstancato
 * Author URI: http://www.claudiosmweb.com/
 * Version: 1.0.0
 * License: GPLv2 or later
 * Text Domain: wcii
 * Domain Path: /languages/
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) exit;

// Sets the plugin path.
define( 'WC_INSTALLMENTS_INFO_PATH', plugin_dir_path( __FILE__ ) );

/**
 * Load the translation.
 */
function wcii_plugin() {
    load_plugin_textdomain( 'wcii', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
}

add_action( 'plugins_loaded', 'wcii_plugin', 0 );

/**
 * WooCommerce fallback notice.
 */
function wcii_fallback_notice() {
    $message = '<div class="error">';
        $message .= '<p>' . sprintf( __( 'WooCommerce Credit Card Interest Table depends on <a href="%s">WooCommerce</a> to work!' , 'wcii' ), esc_url( 'http://wordpress.org/extend/plugins/woocommerce/' ) ) . '</p>';
    $message .= '</div>';

    echo $message;
}

/**
 * Checks if WooCommerce is active.
 *
 * @return bool true if WooCommerce is active, false otherwise.
 */
function wcii_is_woocommerce_active() {

    $active_plugins = (array) get_option( 'active_plugins', array() );

    if ( is_multisite() )
        $active_plugins = array_merge( $active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );

    return in_array( 'woocommerce/woocommerce.php', $active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', $active_plugins );
}

/**
 * Check if WooCommerce is active.
 *
 * Ref: http://wcdocs.woothemes.com/codex/extending/create-a-plugin/.
 */
if ( ! wcii_is_woocommerce_active() ) {
    add_action( 'admin_notices', 'wcii_fallback_notice' );

    return;
}

// Include the classes.
require_once WC_INSTALLMENTS_INFO_PATH . 'includes/class-wc-installments-info.php';
require_once WC_INSTALLMENTS_INFO_PATH . 'includes/class-wc-installments-info-admin.php';
$wc_installments_info_admin = new WC_Installments_Info();
$wc_installments_info = new WC_Installments_Info();
