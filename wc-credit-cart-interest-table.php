<?php
/**
 * Plugin Name: WooCommerce Credit Card Interest Table
 * Plugin URI: http://www.claudiosmweb.com/
 * Description: Credit Card Interest Table
 * Author: claudiosanches, rstancato
 * Author URI: http://www.claudiosmweb.com/
 * Version: 1.0
 * License: GPLv2 or later
 * Text Domain: wcccit
 * Domain Path: /languages/
 */

/**
 * WC_CreditCardInterestTable class.
 */
class WC_CreditCardInterestTable {

    /**
     * Construct.
     */
    public function __construct() {

        // Load textdomain.
        add_action( 'plugins_loaded', array( &$this, 'languages' ), 0 );

        // Default options.
        register_activation_hook( __FILE__, array( &$this, 'default_settings' ) );

        // Add menu.
        add_action( 'admin_menu', array( &$this, 'menu' ) );

        // Init plugin options form.
        add_action( 'admin_init', array( &$this, 'plugin_settings' ) );

        // Add view in WooCommerce products single.
        $views = get_option( 'wcccit_design' );

        if ( $views['display'] == 0 ) {
            add_action( 'woocommerce_after_single_product_summary', array( &$this, 'print_view' ), 50 );

        } else if ( $views['display'] == 1 ) {
            add_action( 'woocommerce_after_single_product_summary', array( &$this, 'print_view' ), 1 );

        } else if ( $views['display'] == 2 ) {
            add_action( 'woocommerce_product_tabs', array( &$this, 'tab_view' ), 60 );
            add_action( 'woocommerce_product_tab_panels', array( &$this, 'display_tab_view' ), 60 );
        } else if ( $views['display'] == 3 ) {
            add_action( 'woocommerce_single_product_summary', array( &$this, 'print_view' ), 35 );
        }

        // Add Shortcode.
        add_shortcode( 'wcccit', array( &$this, 'shortcode' ) );
        add_action( 'init', array( &$this, 'shortcode_buttons_init' ) );

        if ( isset( $_GET['page'] ) && $_GET['page'] == 'wcccit' ) {
            // Back-end scripts.
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );
        }

        // Front-end scripts.
        add_action( 'wp_enqueue_scripts', array( &$this, 'front_scripts' ) );
    }

    /**
     * Load translations.
     */
    public function languages() {
        load_plugin_textdomain( 'wcccit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Set default settings.
     */
    public function default_settings() {

        $settings = array(
            'parcel_maximum'   => '12',
            'parcel_minimum'   => '1',
            'iota'             => '5',
            'without_interest' => '1',
            'interest'         => '2.49',
            'calculation_type' => '0'
        );

        add_option( 'wcccit_settings', $settings );

        $design = array(
            'display' => '0',
            'title'   => __( 'Credit Card Parcels', 'wcccit' ),
            'float'   => 'none',
            'width'   => '100%',
            'border'  => '#DDDDDD',
            'odd'     => '#F0F9E6',
            'even'    => '#FFFFFF',
            'without' => '#006600'
        );

        add_option( 'wcccit_design', $design );


    }

    /**
     * Admin Scripts.
     */
    public function admin_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'farbtastic' );
        wp_enqueue_style( 'farbtastic' );
        wp_register_style( 'wcccit', plugins_url( 'css/styles.css', __FILE__ ), array(), null, 'all' );
        wp_enqueue_style( 'wcccit' );
    }

    /**
     * Front-End Scripts.
     */
    public function front_scripts() {
        if ( is_product() ) {
            wp_register_style( 'wcccit', plugins_url( 'css/styles.css', __FILE__ ), array(), null, 'all' );
            wp_enqueue_style( 'wcccit' );
        }
    }

    /**
     * Add Credit Card Interest Table menu.
     */
    public function menu() {
        add_submenu_page( 'woocommerce', __( 'Credit Card Interest Table', 'wcccit' ), __( 'Credit Card Interest Table', 'wcccit' ), 'manage_options', 'wcccit', array( &$this, 'settings_page' ) );
    }

    /**
     * Built the options page.
     */
    public function settings_page() {
        // Create tabs current class.
        $current_tab = '';
        if ( isset($_GET['tab'] ) ) {
            $current_tab = $_GET['tab'];
        } else {
            $current_tab = 'settings';
        }

        ?>
            <div class="wrap">
                <?php screen_icon( 'options-general' ); ?>
                <h2 class="nav-tab-wrapper">
                <a href="admin.php?page=wcccit&amp;tab=settings" class="nav-tab <?php echo $current_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'wcccit' ); ?></a><a href="admin.php?page=wcccit&amp;tab=design" class="nav-tab <?php echo $current_tab == 'design' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Design', 'wcccit' ); ?></a><a href="admin.php?page=wcccit&amp;tab=icons" class="nav-tab <?php echo $current_tab == 'icons' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Icons', 'wcccit' ); ?></a>
                </h2>
                <?php settings_errors(); ?>
                <form method="post" action="options.php">
                    <?php
                        if ( $current_tab == 'design' ) {
                            settings_fields( 'wcccit_design' );
                            do_settings_sections( 'wcccit_design' );
                        } elseif ( $current_tab == 'icons' ) {
                            settings_fields( 'wcccit_icons' );
                            do_settings_sections( 'wcccit_icons' );
                        } else {
                            settings_fields( 'wcccit_settings' );
                            do_settings_sections( 'wcccit_settings' );
                        }

                        submit_button();
                    ?>
                </form>
            </div>
        <?php
    }

    /**
     *  Plugin settings form fields.
     */
    public function plugin_settings() {
        $option = 'wcccit_settings';
        $design = 'wcccit_design';
        $icons = 'wcccit_icons';

        // Create option in wp_options.
        if ( get_option( $option ) == false ) {
            add_option( $option );
        }
        if ( get_option( $design ) == false ) {
            add_option( $design );
        }
        if ( get_option( $icons ) == false ) {
            add_option( $icons );
        }

        // set Section.
        add_settings_section(
            'settings_section',
            __( 'Credit Card Interest Settings', 'wcccit' ),
            '__return_false',
            $option
        );

        add_settings_field(
            'parcel_maximum',
            __( 'Number of parcels', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $option,
            'settings_section',
            array(
                'menu' => $option,
                'id' => 'parcel_maximum',
                'default' => '12'
            )
        );

        add_settings_field(
            'parcel_minimum',
            __( 'Parcel minimum', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $option,
            'settings_section',
            array(
                'menu' => $option,
                'id' => 'parcel_minimum',
                'default' => '1'
            )
        );

        add_settings_field(
            'iota',
            __( 'iota', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $option,
            'settings_section',
            array(
                'menu' => $option,
                'id' => 'iota',
                'default' => '5'
            )
        );

        add_settings_field(
            'without_interest',
            __( 'Parcels without interest', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $option,
            'settings_section',
            array(
                'menu' => $option,
                'id' => 'without_interest',
                'default' => '1'
            )
        );

        add_settings_field(
            'interest',
            __( 'Interest', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $option,
            'settings_section',
            array(
                'menu' => $option,
                'id' => 'interest',
                'default' => '2.49'
            )
        );

        add_settings_field(
            'calculation_type',
            __( 'Calculation type', 'wcccit' ),
            array( &$this , 'select_element_callback' ),
            $option,
            'settings_section',
            array(
                'menu' => $option,
                'id' => 'calculation_type',
                'default' => '0',
                'items' => array(
                    '0' => __( 'Amortization schedule', 'wcccit' ),
                    '1' => __( 'Simple interest', 'wcccit' ),
                ),
                'description' => __( 'Amortization schedule: See details in <a href="http://en.wikipedia.org/wiki/Amortization_schedule">Wikipedia</a><br />Simple interest: See details in <a href="http://en.wikipedia.org/wiki/Simple_interest#Simple_interest">Wikipedia</a>', 'wcccit' )
            )
        );

        // Set Section.
        add_settings_section(
            'design_section',
            __( 'Table Design', 'wcccit' ),
            '__return_false',
            $design
        );

        add_settings_field(
            'display',
            __( 'Display in', 'wcccit' ),
            array( &$this , 'select_element_callback' ),
            $design,
            'design_section',
            array(
                'menu' => $design,
                'id' => 'display',
                'default' => '0',
                'items' => array(
                    '0' => __( 'Product bottom', 'wcccit' ),
                    '1' => __( 'Before product tab', 'wcccit' ),
                    '2' => __( 'Product tab', 'wcccit' ),
                    '3' => __( 'After add to cart button', 'wcccit' ),
                    '4' => __( 'No display', 'wcccit' ),
                )
            )
        );

        add_settings_field(
            'title',
            __( 'Table Title', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $design,
            'design_section',
            array(
                'menu' => $design,
                'id' => 'title',
                'default' => __( 'Credit Card Parcels', 'wcccit' ),
                'class' => 'regular-text'
            )
        );

        // Set Section.
        add_settings_section(
            'styles_section',
            __( 'Table Styles', 'wcccit' ),
            '__return_false',
            $design
        );

        add_settings_field(
            'float',
            __( 'Float', 'wcccit' ),
            array( &$this , 'select_element_callback' ),
            $design,
            'styles_section',
            array(
                'menu' => $design,
                'id' => 'float',
                'default' => 'none',
                'items' => array(
                    'none' => __( 'None', 'wcccit' ),
                    'left' => __( 'Left', 'wcccit' ),
                    'right' => __( 'Right', 'wcccit' ),
                    'center' => __( 'Center', 'wcccit' ),
                )
            )
        );

        add_settings_field(
            'width',
            __( 'Width', 'wcccit' ),
            array( &$this , 'text_element_callback' ),
            $design,
            'styles_section',
            array(
                'menu' => $design,
                'id' => 'width',
                'default' => '100%',
                'description' => __( 'Value with %, px or em', 'wcccit' )
            )
        );

        add_settings_field(
            'border',
            __( 'Border color', 'wcccit' ),
            array( &$this , 'color_element_callback' ),
            $design,
            'styles_section',
            array(
                'menu' => $design,
                'id' => 'border',
                'default' => '#DDDDDD',
            )
        );

        add_settings_field(
            'odd',
            __( 'Odd background' , 'wcccit' ),
            array( &$this , 'color_element_callback' ),
            $design,
            'styles_section',
            array(
                'menu' => $design,
                'id' => 'odd',
                'default' => '#F0F9E6',
            )
        );

        add_settings_field(
            'even',
            __( 'Even background', 'wcccit' ),
            array( &$this , 'color_element_callback' ),
            $design,
            'styles_section',
            array(
                'menu' => $design,
                'id' => 'even',
                'default' => '#FFFFFF',
            )
        );

        add_settings_field(
            'without',
            __( 'Without interest color', 'wcccit' ),
            array( &$this , 'color_element_callback' ),
            $design,
            'styles_section',
            array(
                'menu' => $design,
                'id' => 'without',
                'default' => '#006600',
            )
        );

        // Set Section.
        add_settings_section(
            'icons_section',
            __( '', 'wcccit' ),
            '__return_false',
            $icons
        );

        add_settings_field(
            'cards',
            __( 'Cards', 'wcccit' ),
            array( &$this , 'checkbox_cards_element_callback' ),
            $icons,
            'icons_section',
            array(
                'menu' => $icons,
                'id' => 'cards',
                'items' => array(
                    'visa' => 'visa',
                    'master' => 'master',
                    'hypercard' => 'hypercard',
                    'american' => 'american',
                    'diners' => 'diners',
                    'aura' => 'aura'
                )
            )
        );

        // Register settings.
        register_setting( $option, $option, array( &$this, 'validate_options' ) );
        register_setting( $icons, $icons, array( &$this, 'validate_options' ) );
        register_setting( $design, $design, array( &$this, 'validate_options' ) );
    }

    /**
     * Text element fallback.
     */
    public function text_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        $class = isset( $args['class'] ) ? $args['class'] : 'small-text';

        $options = get_option( $menu );

        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }

        $html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="%4$s" />', $id, $menu, $current, $class );

        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        echo $html;
    }

    /**
     * Select element fallback.
     */
    public function select_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        $items = $args['items'];

        $options = get_option( $menu );

        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '';
        }

        $html = sprintf( '<select id="%1$s" name="%2$s[%1$s]">', $id, $menu );
        foreach( $items as $key => $label ) {
            $key = sanitize_title( $key );

            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
        }
        $html .= '</select>';

        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        echo $html;
    }

    /**
     * Select element fallback.
     */
    public function checkbox_cards_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];
        $items = $args['items'];

        $options = get_option( $menu );

        $count = 0;
        $html = '';
        foreach( $items as $key => $label ) {
            $item_name = $menu . '[' . $count . ']';

            // Sets current option.
            if ( isset( $options[$count] ) ) {
                $current = $options[$count];
            } else {
                $current = isset( $args['default'] ) ? $args['default'] : '';
            }

            $html .= '<div class="card-item">';
            $html .= sprintf( '<input type="checkbox" id="%2$s-%4$s" name="%1$s" value="%4$s"%3$s />', $item_name, $menu, checked( $current, $key, false ), $key );
            $html .= sprintf( '<label for="%s-%s"> <div class="card-icons card-%s"></div></label>', $menu, $key, $label );
            $html .= '<br style="clear: both;" /></div>';

            $count++;
        }

        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        echo $html;
    }

    /**
     * Color element fallback.
     */
    function color_element_callback( $args ) {
        $menu = $args['menu'];
        $id = $args['id'];

        $options = get_option( $menu );

        if ( isset( $options[$id] ) ) {
            $current = $options[$id];
        } else {
            $current = isset( $args['default'] ) ? $args['default'] : '#ffffff';
        }

        $html = sprintf( '<input type="text" id="color-%1$s" name="%2$s[%1$s]" value="%3$s" class="regular-text" style="width: 75px" />', $id, $menu, $current );

        // Displays option description.
        if ( isset( $args['description'] ) ) {
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );
        }

        $html .= sprintf( '<div id="farbtasticbox-%s"></div>', $id );

        $html .= '<script type="text/javascript">';
            $html .= 'jQuery(document).ready(function($) {';
                $html .= sprintf( '$("#farbtasticbox-%s").hide();', $id );
                $html .= sprintf( '$("#farbtasticbox-%1$s").farbtastic("#color-%1$s");', $id );
                $html .= sprintf( '$("#color-%s").click(function(){', $id );
                    $html .= sprintf( '$("#farbtasticbox-%s").slideToggle()', $id );
                $html .= '});';
            $html .= '});';
        $html .= '</script>';

        echo $html;
    }

    /**
     * Valid options.
     *
     * @param  array $input options to valid.
     * @return array        validated options.
     */
    public function validate_options( $input ) {
        // Create our array for storing the validated options.
        $output = array();

        // Loop through each of the incoming options.
        foreach ( $input as $key => $value ) {

            // Check to see if the current option has a value. If so, process it.
            if ( isset( $input[$key] ) ) {

                // Strip all HTML and PHP tags and properly handle quoted strings.
                $output[$key] = strip_tags( stripslashes( $input[$key] ) );
            }
        }

        // Return the array processing any additional functions filtered by this action.
        return apply_filters( 'wcccit_validate_input', $output, $input );
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

        if ( get_option( 'woocommerce_price_trim_zeros' ) == 'yes' && $num_decimals > 0 ) {
            $price = woocommerce_trim_zeros( $price );
        }

        return $price;
    }

    /**
     * Format price.
     *
     * @param  string $price Product price.
     * @return string        Price formatted.
     */
    public function format_price( $price ) {
        global $woocommerce;

        $return = '';
        $currency_pos = get_option( 'woocommerce_currency_pos' );
        $currency_symbol = get_woocommerce_currency_symbol();

        $price = $this->number_format( $price );

        switch ( $currency_pos ) {
            case 'left' :
                $return = $currency_symbol . $price;
            break;
            case 'right' :
                $return = $price . $currency_symbol;
            break;
            case 'left_space' :
                $return = $currency_symbol . '&nbsp;' . $price;
            break;
            case 'right_space' :
                $return = $price . '&nbsp;' . $currency_symbol;
            break;
        }

        return $return;
    }

    /**
     * Installment configurations.
     *
     * @param  float  $price            Product price.
     * @param  int    $parcel_maximum   Quantity of parcels.
     * @param  int    $parcel_minimum   Parcels minimum.
     * @param  mixed  $iota             iota.
     * @param  int    $without_interest Parcels without interest.
     * @param  float  $interest         Interest.
     * @param  int    $calculation_type Calculation type (0 - Amortization schedule or 1 - Simple interest);
     * @return string                   Table with parcels and interest.
     */
    public function view(
        $price,
        $parcel_maximum,
        $parcel_minimum,
        $iota,
        $without_interest,
        $interest,
        $calculation_type ) {

        // Get design options.
        $design = get_option( 'wcccit_design' );

        // Get icons
        $icons = get_option( 'wcccit_icons', '' );

        // Float ou margin.
        $align = ( $design['float'] == 'center' ) ? ' margin-left: auto; margin-right: auto' : ' float: ' . $design['float'];

        $table = '';

        if ( $price > $iota ) {

            // Open the table.
            $table .= '<div id="wc-credit-cart-table" class="clear" style="width: ' . $design['width'] . ';' . $align . ';">';

            if ( $design['display'] != 2 ) {
                $table .= '<h3>' . $design['title'] . '</h3>';
            }

            // Border wrapper.
            $table .= '<div id="wc-credit-cart-table-wrap" style="border-color:' . $design['border'] . ';">';

            $table .= '<div class="part left">';

            $count = 0;
            for ( $p = $parcel_minimum; $p <= $parcel_maximum; $p++ ) {

                $background = ( $count %  2 ) ? $design['even'] : $design['odd'];

                // Without interest.
                if ( $p <= $without_interest ) {
                    $parcel_value = $price / $p;
                }

                // With interest.
                if ( $p > $without_interest ) {

                    if ( $calculation_type == 0 ) {
                        $parcel_value = ( $price * ( $interest / 100 ) ) / ( 1 - ( 1 / ( pow( 1 + ( $interest / 100 ), $p ) ) ) );
                    }

                    if ( $calculation_type == 1 ) {
                        $parcel_value = ( $price * pow( 1 + ( $interest / 100 ), $p ) ) / $p;
                    }

                }

                // Test iota.
                if ( $parcel_value >= $iota ) {

                    if ( $p <= $without_interest ) {
                        $table .= '<span class="card-info" style="color: ' . $design['without'] . '; background: ' . $background . '; border-color: ' . $design['border'] . ';">' . sprintf( __( '%s%sx%s of %s %swithout interest%s', 'wcccit' ), '<strong>', $p, '</strong>', $this->format_price( $parcel_value ), '<em>', '</em>' ) . '</span>';
                    } else {
                        $table .= '<span class="card-info" style="background: ' . $background . '; border-color: ' . $design['border'] . ';">' . sprintf( __( '%s%sx%s of %s %swith interest%s', 'wcccit' ), '<strong>', $p, '</strong>' , $this->format_price( $parcel_value ), '<em>', '</em>' ) . '</span>';
                    }

                }

                if ( $p == intval( $parcel_maximum / 2 ) ) {
                    $table .= '</div>';

                    $table .= '<div class="part right">';
                }

                $count++;
            }

            $table .= '</div>';

            // Close the border wrapper.
            $table .= '<div class="clear"></div>';
            $table .= '</div>';

            // Details.
            $table .= '<div id="wc-credit-cart-table-details">';

            // Show interest info.
            if ( $without_interest < $parcel_maximum ) {
                $table .= '<span>' . sprintf( __( 'Interest of %s%s per month', 'wcccit' ), $this->number_format( $interest ), '%' ) . '</span>';
            }

            // Show maximum parcel info.
            if ( $iota > 0 ) {
                $table .= '<span>' . sprintf( __( ' (parcel minimum of %s)', 'wcccit' ), $this->format_price( $iota ) ) . '</span>';
            }

            // Close the details.
            $table .= '</div>';

            // Display credit card icons.
            if ( $icons != '' ) {

                $table .= '<div id="wc-credit-cart-table-icons">';
                $table .= '<strong>' . __( 'Pay with: ', 'wcccit' ) . '</strong>';

                foreach ( $icons as $key => $value ) {
                    $table .= sprintf( '<span class="card-icons card-%s"></span>', $value );
                }

                $table .= '<div class="clear"></div>';
                $table .= '</div>';
            }


            // Close the table.
            $table .= '</div>';
        }

        return $table;
    }

    /**
     * Print table view.
     */
    public function print_view() {
        global $product;

        // Get settings.
        $default = get_option( 'wcccit_settings' );

        echo $this->view( $product->get_price(), $default['parcel_maximum'], $default['parcel_minimum'], $default['iota'], $default['without_interest'], $default['interest'], $default['calculation_type'] );
    }

    /**
     * Create a new tab.
     */
    public function tab_view() {
        echo '<li class="wcccit_tab"><a href="#tab-wcccit">' . apply_filters( 'wcccit_tab_title' , __( 'Credit Card Parcels', 'wcccit' ) ) . '</a></li>';
    }

    /**
     * Display tab content.
     */
    public function display_tab_view() {
        global $product;

        // Get settings.
        $default = get_option( 'wcccit_settings' );

        $html = '<div class="panel entry-content" id="tab-wcccit">';
            $html .= $this->view( $product->price, $default['parcel_maximum'], $default['parcel_minimum'], $default['iota'], $default['without_interest'], $default['interest'], $default['calculation_type'] );
        $html .= '</div>';

        echo $html;
    }

    /**
     * Table shortcode.
     */
    public function shortcode( $atts ) {
        global $product;

        // Get default settings.
        $default = get_option( 'wcccit_settings' );

        extract(
            shortcode_atts(
                array(
                    'price'            => $product->price,
                    'parcel_maximum'   => $default['parcel_maximum'],
                    'parcel_minimum'   => $default['parcel_minimum'],
                    'iota'             => $default['iota'],
                    'without_interest' => $default['without_interest'],
                    'interest'         => $default['interest'],
                    'calculation_type' => $default['calculation_type']
                ), $atts
            )
        );

        return $this->view( $price, $parcel_maximum, $parcel_minimum, $iota, $without_interest, $interest, $calculation_type );
    }

    /**
     * Add custom buttons in TinyMCE.
     */
    public function shortcode_register_buttons( $buttons ) {
        array_push( $buttons, '|', 'wcccit' );
        return $buttons;
    }

    /**
     * Register button scripts.
     */
    public function shortcode_add_buttons( $plugin_array ) {
        $plugin_array['wcccit'] = plugins_url( 'tinymce/wcccit.js' , __FILE__ );
        return $plugin_array;
    }

    /**
     * Register buttons in init.
     */
    public function shortcode_buttons_init() {
        if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
            return;
        }

        if ( get_user_option( 'rich_editing') == 'true' ) {
            add_filter( 'mce_external_plugins', array( &$this, 'shortcode_add_buttons' ) );
            add_filter( 'mce_buttons', array( &$this, 'shortcode_register_buttons' ) );
        }
    }

}

/**
 * WooCommerce fallback notice.
 */
function wcccit_fallback_notice() {
    $message = '<div class="error">';
        $message .= '<p>' . sprintf( __( 'WooCommerce Credit Card Interest Table depends on <a href="%s">WooCommerce</a> to work!' , 'wcccit' ), esc_url( 'http://wordpress.org/extend/plugins/woocommerce/' ) ) . '</p>';
    $message .= '</div>';

    echo $message;
}

/**
 * Check if WooCommerce is active.
 *
 * Ref: http://wcdocs.woothemes.com/codex/extending/create-a-plugin/.
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    $wcCreditCardInterestTable = new WC_CreditCardInterestTable();
} else {
    add_action( 'admin_notices', 'wcccit_fallback_notice' );
}

