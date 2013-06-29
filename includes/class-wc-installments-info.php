<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WC_Installments_Info class.
 */
class WC_Installments_Info {

    /**
     * Construct.
     */
    public function __construct() {
        $this->settings = get_option( 'wcii_settings' );
        $this->design = get_option( 'wcii_design' );

        // Displays the table.
        $this->table_display();

        // Add Shortcode.
        add_shortcode( 'wcii', array( &$this, 'shortcode' ) );
        add_action( 'init', array( &$this, 'shortcode_buttons_init' ) );

        // Front-end scripts.
        add_action( 'wp_enqueue_scripts', array( &$this, 'front_scripts' ) );
    }

    /**
     * Front-End Scripts.
     */
    public function front_scripts() {
        if ( is_product() )
            wp_enqueue_style( 'wcii-styles', WC_INSTALLMENTS_INFO_URL . 'assets/css/styles.css', array(), null, 'all' );
    }

    /**
     * Displays the table in the products single.
     *
     * @return void
     */
    protected function table_display() {
        if ( 0 == $this->design['display'] ) {
            add_action( 'woocommerce_after_single_product_summary', array( &$this, 'print_view' ), 50 );

        } else if ( 1 == $this->design['display'] ) {
            add_action( 'woocommerce_after_single_product_summary', array( &$this, 'print_view' ), 1 );

        } else if ( 2 == $this->design['display'] ) {
            add_action( 'woocommerce_product_tabs', array( &$this, 'tab_view' ), 60 );
            add_action( 'woocommerce_product_tab_panels', array( &$this, 'display_tab_view' ), 60 );
        } else if ( 3 == $this->design['display'] ) {
            add_action( 'woocommerce_single_product_summary', array( &$this, 'print_view' ), 35 );
        }
    }

    /**
     * Number format.
     *
     * @param  float  $price Number of Product price.
     * @return string        Number formatted.
     */
    public function number_format( $price ) {
        global $woocommerce;

        $num_decimals = (int) get_option( 'woocommerce_price_num_decimals' );

        $price = apply_filters( 'raw_woocommerce_price', (double) $price );

        $price = number_format( $price, $num_decimals, stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) );

        if ( 'yes' == get_option( 'woocommerce_price_trim_zeros' ) && 0 < $num_decimals )
            $price = woocommerce_trim_zeros( $price );

        return $price;
    }

    /**
     * Installment calculator.
     *
     * @param  float  $price               Product price.
     * @param  int    $installment_maximum Quantity of installments.
     * @param  int    $installment_minimum Installments minimum.
     * @param  mixed  $iota                iota.
     * @param  int    $without_interest    Installments without interest.
     * @param  float  $interest            Interest.
     * @param  int    $calculation_type    Calculation type (0 - Amortization schedule or 1 - Simple interest);
     *
     * @return array                       The installments.
     */
    protected function installment_calculator( $price, $installment_maximum, $installment_minimum, $iota, $without_interest, $interest, $calculation_type ) {

        $calcule = array();

        if ( $price > $iota ) {
            for ( $i = $installment_minimum; $i <= $installment_maximum; $i++ ) {

                // Without interest.
                if ( $i <= $without_interest )
                    $installment_value = $price / $i;

                // With interest.
                if ( $i > $without_interest ) {
                    if ( 0 == $calculation_type )
                        $installment_value = ( $price * ( $interest / 100 ) ) / ( 1 - ( 1 / ( pow( 1 + ( $interest / 100 ), $i ) ) ) );

                    if ( 1 == $calculation_type )
                        $installment_value = ( $price * pow( 1 + ( $interest / 100 ), $i ) ) / $i;
                }

                // Test iota.
                if ( $installment_value >= $iota )
                    $calcule[] = woocommerce_price( $installment_value );
                else
                    break;
            }
        }

        return $calcule;
    }

    /**
     * Installment view.
     *
     * @param  float  $price               Product price.
     * @param  int    $installment_maximum Quantity of installments.
     * @param  int    $installment_minimum Installments minimum.
     * @param  mixed  $iota                iota.
     * @param  int    $without_interest    Installments without interest.
     * @param  float  $interest            Interest.
     * @param  int    $calculation_type    Calculation type (0 - Amortization schedule or 1 - Simple interest);
     *
     * @return string                      Table with installments and interest.
     */
    protected function view(
        $price,
        $installment_maximum,
        $installment_minimum,
        $iota,
        $without_interest,
        $interest,
        $calculation_type ) {

        $table = '';

        if ( ! empty( $price ) ) {
            $values = $this->installment_calculator( $price, $installment_maximum, $installment_minimum, $iota, $without_interest, $interest, $calculation_type );

            if ( ! empty( $values ) ) {

                // Float ou margin.
                $align = ( $this->design['float'] == 'center' ) ? ' margin-left: auto; margin-right: auto' : ' float: ' . $this->design['float'];

                $middle = ceil( count( $values ) / 2 );

                // Open the table.
                $table .= '<div id="wc-credit-cart-table" class="clear" style="width: ' . $this->design['width'] . ';' . $align . ';">';

                if ( 2 != $this->design['display'] )
                    $table .= '<h3>' . $this->design['title'] . '</h3>';

                // Border wrapper.
                $table .= '<div id="wc-credit-cart-table-wrap" style="border-color:' . $this->design['border'] . ';">';

                $table .= '<div class="part left">';

                // Displays the installments.
                foreach ( $values as $key => $value ) {
                    $background = ( $key % 2 ) ? $this->design['even'] : $this->design['odd'];

                    $key++;

                    // Tests if has interest.
                    if ( $key <= $without_interest )
                        $table .= '<span class="card-info" style="color: ' . $this->design['without'] . '; background: ' . $background . '; border-color: ' . $this->design['border'] . ';">' . sprintf( __( '%s%sx%s of %s %swithout interest%s', 'wcii' ), '<strong>', $key, '</strong>', $value, '<em>', '</em>' ) . '</span>';
                    else
                        $table .= '<span class="card-info" style="background: ' . $background . '; border-color: ' . $this->design['border'] . ';">' . sprintf( __( '%s%sx%s of %s %swith interest%s', 'wcii' ), '<strong>', $key, '</strong>', $value, '<em>', '</em>' ) . '</span>';

                    // Divides the table.
                    if ( $key == $middle )
                        $table .= '</div><div class="part right">';
                }

                $table .= '</div>';

                // Close the border wrapper.
                $table .= '<div class="clear"></div>';
                $table .= '</div>';

                // Details.
                $table .= '<div id="wc-credit-cart-table-details">';

                // Show interest info.
                if ( $without_interest < $installment_maximum )
                    $table .= '<span>' . sprintf( __( 'Interest of %s%s per month', 'wcii' ), $this->number_format( $interest ), '%' ) . '</span>';

                // Show maximum installment info.
                if ( $iota > 0 )
                    $table .= '<span>' . sprintf( __( ' (installment minimum of %s)', 'wcii' ), woocommerce_price( $iota ) ) . '</span>';

                // Close the details.
                $table .= '</div>';

                // Close the table.
                $table .= '</div>';

                // Display credit card icons.
                // if ( $icons != '' ) {

                //     $table .= '<div id="wc-credit-cart-table-icons">';
                //     $table .= '<strong>' . __( 'Pay with: ', 'wcii' ) . '</strong>';

                //     foreach ( $icons as $key => $value ) {
                //         $table .= sprintf( '<span class="card-icons card-%s"></span>', $value );
                //     }

                //     $table .= '<div class="clear"></div>';
                //     $table .= '</div>';
                // }

            }
        }

        return $table;
    }

    /**
     * Print table view.
     */
    public function print_view() {
        global $product;

        echo $this->view( $product->get_price(), $this->settings['installment_maximum'], $this->settings['installment_minimum'], $this->settings['iota'], $this->settings['without_interest'], $this->settings['interest'], $this->settings['calculation_type'] );
    }

    /**
     * Create a new tab.
     */
    public function tab_view() {
        echo '<li class="wcii_tab"><a href="#tab-wcii">' . apply_filters( 'installments_info_tab_title' , __( 'Credit Card Installments', 'wcii' ) ) . '</a></li>';
    }

    /**
     * Display tab content.
     */
    public function display_tab_view() {
        global $product;

        echo '<div class="panel entry-content" id="tab-wcii">';
            $this->print_view();
        echo '</div>';
    }

    /**
     * Table shortcode.
     */
    public function shortcode( $atts ) {
        global $product;

        extract(
            shortcode_atts(
                array(
                    'price'               => $product->price,
                    'installment_maximum' => $this->settings['installment_maximum'],
                    'installment_minimum' => $this->settings['installment_minimum'],
                    'iota'                => $this->settings['iota'],
                    'without_interest'    => $this->settings['without_interest'],
                    'interest'            => $this->settings['interest'],
                    'calculation_type'    => $this->settings['calculation_type']
                ), $atts
            )
        );

        return $this->view( $price, $installment_maximum, $installment_minimum, $iota, $without_interest, $interest, $calculation_type );
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
        $plugin_array['wcii'] = plugins_url( 'tinymce/wcii.js' , __FILE__ );

        return $plugin_array;
    }

    /**
     * Register buttons in init.
     */
    public function shortcode_buttons_init() {
        if ( ! current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) )
            return;

        if ( 'true' == get_user_option( 'rich_editing') ) {
            add_filter( 'mce_external_plugins', array( &$this, 'shortcode_add_buttons' ) );
            add_filter( 'mce_buttons', array( &$this, 'shortcode_register_buttons' ) );
        }
    }

}
