<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WC_Installments_Info_Shortcode class.
 */
class WC_Installments_Info_Shortcode {

    /**
     * Construct.
     */
    public function __construct() {

        // Add Shortcode.
        add_shortcode( 'wcii', array( &$this, 'shortcode' ) );

        // Add a button in the editor.
        add_action( 'init', array( &$this, 'shortcode_buttons_init' ) );

        // Register the modal dialog ajax request.
        add_action( 'wp_ajax_wcii_tinymce_dialog', array( $this, 'dialog' ) );
    }

    /**
     * Table shortcode.
     */
    public function shortcode( $atts ) {
        global $product, $wc_installments_info;

        // Gets the settings.
        $settings = $wc_installments_info->settings;

        extract(
            shortcode_atts(
                array(
                    'price'               => $product->price,
                    'installment_maximum' => $settings['installment_maximum'],
                    'installment_minimum' => $settings['installment_minimum'],
                    'iota'                => $settings['iota'],
                    'without_interest'    => $settings['without_interest'],
                    'interest'            => $settings['interest'],
                    'calculation_type'    => $settings['calculation_type']
                ), $atts
            )
        );

        return $wc_installments_info->view( $price, $installment_maximum, $installment_minimum, $iota, $without_interest, $interest, $calculation_type );
    }

    /**
     * Add custom buttons in TinyMCE.
     */
    public function shortcode_register_buttons( $buttons ) {
        array_push( $buttons, '|', 'wcii' );

        return $buttons;
    }

    /**
     * Register button scripts.
     */
    public function shortcode_add_buttons( $plugin_array ) {
        $plugin_array['wcii'] = WC_INSTALLMENTS_INFO_URL . 'assets/js/tinymce.wcii.button.js';

        return $plugin_array;
    }

    /**
     * Register buttons in init.
     */
    public function shortcode_buttons_init() {
        if ( ! current_user_can( 'edit_posts' ) && ! current_user_can( 'edit_pages' ) )
            return;

        if ( 'true' == get_user_option( 'rich_editing') ) {
            add_filter( 'mce_external_plugins', array( &$this, 'shortcode_add_buttons' ) );
            add_filter( 'mce_buttons', array( &$this, 'shortcode_register_buttons' ) );
        }
    }

    /**
     * Displays the shortcode modal dialog.
     *
     * @return string  Modal Dialog HTML.
     */
    function dialog() {
        @ob_clean();

        require_once WC_INSTALLMENTS_INFO_PATH . 'includes/tinymce-dialog.php';

        die();
    }
}
