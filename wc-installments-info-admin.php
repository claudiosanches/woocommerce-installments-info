<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.

/**
 * WC_Installments_Info_Admin class.
 */
class WC_Installments_Info_Admin {

    /**
     * Construct.
     */
    public function __construct() {
        // Add menu.
        add_action( 'admin_menu', array( &$this, 'menu' ) );

        // Init plugin options form.
        add_action( 'admin_init', array( &$this, 'plugin_settings' ) );

        // Back-end scripts.
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'wcccit' )
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

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
}
