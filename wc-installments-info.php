<?php
/**
 * Plugin Name: WooCommerce Installments Info
 * Plugin URI: http://www.claudiosmweb.com/
 * Description: Displays Installments Info in you WooCommerce
 * Author: claudiosanches, rstancato
 * Author URI: http://www.claudiosmweb.com/
 * Version: 2.0.0
 * License: GPLv2 or later
 * Text Domain: wcii
 * Domain Path: /languages/
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

// Sets the definitions
define( 'WC_INSTALLMENTS_INFO_PATH', plugin_dir_path( __FILE__ ) );
define( 'WC_INSTALLMENTS_INFO_URL', plugin_dir_url( __FILE__ ) );

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
    echo '<div class="error"><p>' . sprintf(
        __( 'WooCommerce Installments Info depends on %s to work!' , 'wcii' ),
        '<a href="http://wordpress.org/extend/plugins/woocommerce/">WooCommerce</a>'
    ) . '</p></div>';
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
require_once WC_INSTALLMENTS_INFO_PATH . 'includes/class-wc-installments-info-shortcode.php';
$wc_installments_info_admin = new WC_Installments_Info_Admin();
$wc_installments_info = new WC_Installments_Info();
$wc_installments_info_shortcode = new WC_Installments_Info_Shortcode();

/**
 * Gets the table.
 *
 * @return string The table in HTML.
 */
function wcii_get_table() {
    global $wc_installments_info;

    $wc_installments_info->print_view();
}
