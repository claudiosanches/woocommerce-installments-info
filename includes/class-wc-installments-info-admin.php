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
        if ( isset( $_GET['page'] ) && $_GET['page'] == 'wc-installments-info' )
            add_action( 'admin_enqueue_scripts', array( &$this, 'admin_scripts' ) );

    }

    /**
     * Set default settings.
     */
    public function default_settings() {

        $settings = array(
            'installment_maximum' => 12,
            'installment_minimum' => 1,
            'iota'                => 5,
            'without_interest'    => 1,
            'interest'            => 2.49,
            'calculation_type'    => 0
        );

        add_option( 'wcii_settings', $settings );

        $design = array(
            'display' => 0,
            'title'   => __( 'Credit Card Installments', 'wcii' ),
            'float'   => 'none',
            'width'   => '100%',
            'border'  => '#DDDDDD',
            'odd'     => '#F0F9E6',
            'even'    => '#FFFFFF',
            'without' => '#006600'
        );

        add_option( 'wcii_design', $design );
    }

    /**
     * Load options scripts.
     *
     * @return void
     */
    public function admin_scripts() {
        // jQuery.
        wp_enqueue_script( 'jquery' );

        // Color Picker.
        wp_enqueue_style( 'wp-color-picker' );
        wp_enqueue_script( 'wp-color-picker' );

        // Plugin scripts.
        wp_enqueue_script( 'wcii-admin', WC_INSTALLMENTS_INFO_URL . 'assets/js/admin.js', array(), null, 'all' );

        // Plugin styles.
        wp_enqueue_style( 'wcii-styles', WC_INSTALLMENTS_INFO_URL . 'assets/css/wcii.css', array(), null, 'all' );
    }

    /**
     * Add Installments Info menu.
     */
    public function menu() {
        add_submenu_page(
            'woocommerce',
            __( 'Installments Info', 'wcii' ),
            __( 'Installments Info', 'wcii' ),
            'manage_options',
            'wc-installments-info',
            array( &$this, 'settings_page' )
        );
    }

    /**
     * Built the options page.
     */
    public function settings_page() {
        // Create tabs current class.
        $current_tab = '';
        if ( isset( $_GET['tab'] ) )
            $current_tab = $_GET['tab'];
        else
            $current_tab = 'settings';

        ?>
            <div class="wrap">
                <?php screen_icon( 'options-general' ); ?>
                <h2 class="nav-tab-wrapper">
                <a href="admin.php?page=wc-installments-info&amp;tab=settings" class="nav-tab <?php echo $current_tab == 'settings' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Settings', 'wcii' ); ?></a><a href="admin.php?page=wc-installments-info&amp;tab=design" class="nav-tab <?php echo $current_tab == 'design' ? 'nav-tab-active' : ''; ?>"><?php _e( 'Design', 'wcii' ); ?></a>
                </h2>
                <?php settings_errors(); ?>
                <form method="post" action="options.php">
                    <?php
                        if ( $current_tab == 'design' ) {
                            settings_fields( 'wcii_design' );
                            do_settings_sections( 'wcii_design' );
                        } else {
                            settings_fields( 'wcii_settings' );
                            do_settings_sections( 'wcii_settings' );
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
        $settings = 'wcii_settings';
        $design = 'wcii_design';

        // Create option in wp_options.
        if ( get_option( $settings ) == false )
            add_option( $settings );

        if ( get_option( $design ) == false )
            add_option( $design );


        add_settings_section(
            'wcii_settings_section',
            __( 'Credit Card Interest Settings', 'wcii' ),
            '__return_false',
            $settings
        );

        add_settings_field(
            'installment_maximum',
            __( 'Number of Installments', 'wcii' ),
            array( &$this , 'callback_text' ),
            $settings,
            'wcii_settings_section',
            array(
                'tab' => $settings,
                'id' => 'installment_maximum',
                'default' => 12
            )
        );

        add_settings_field(
            'installment_minimum',
            __( 'Installment minimum', 'wcii' ),
            array( &$this , 'callback_text' ),
            $settings,
            'wcii_settings_section',
            array(
                'tab' => $settings,
                'id' => 'installment_minimum',
                'default' => 1
            )
        );

        add_settings_field(
            'iota',
            __( 'iota', 'wcii' ),
            array( &$this , 'callback_text' ),
            $settings,
            'wcii_settings_section',
            array(
                'tab' => $settings,
                'id' => 'iota',
                'default' => 5
            )
        );

        add_settings_field(
            'without_interest',
            __( 'Installments without interest', 'wcii' ),
            array( &$this , 'callback_text' ),
            $settings,
            'wcii_settings_section',
            array(
                'tab' => $settings,
                'id' => 'without_interest',
                'default' => 1
            )
        );

        add_settings_field(
            'interest',
            __( 'Interest', 'wcii' ),
            array( &$this , 'callback_text' ),
            $settings,
            'wcii_settings_section',
            array(
                'tab' => $settings,
                'id' => 'interest',
                'default' => '2.49'
            )
        );

        add_settings_field(
            'calculation_type',
            __( 'Calculation type', 'wcii' ),
            array( &$this , 'callback_select' ),
            $settings,
            'wcii_settings_section',
            array(
                'tab' => $settings,
                'id' => 'calculation_type',
                'default' => 0,
                'options' => array(
                    0 => __( 'Amortization schedule', 'wcii' ),
                    1 => __( 'Simple interest', 'wcii' ),
                ),
                'description' => __( 'Amortization schedule: See details in <a href="http://en.wikipedia.org/wiki/Amortization_schedule">Wikipedia</a><br />Simple interest: See details in <a href="http://en.wikipedia.org/wiki/Simple_interest#Simple_interest">Wikipedia</a>', 'wcii' )
            )
        );

        add_settings_section(
            'wcii_table_design_section',
            __( 'Table Design', 'wcii' ),
            '__return_false',
            $design
        );

        add_settings_field(
            'display',
            __( 'Display in', 'wcii' ),
            array( &$this , 'callback_select' ),
            $design,
            'wcii_table_design_section',
            array(
                'tab' => $design,
                'id' => 'display',
                'default' => 0,
                'options' => array(
                    __( 'Product bottom', 'wcii' ),
                    __( 'Before product tab', 'wcii' ),
                    __( 'Product tab', 'wcii' ),
                    __( 'After add to cart button', 'wcii' ),
                    __( 'No display', 'wcii' ),
                )
            )
        );

        add_settings_field(
            'information',
            __( 'Installments information', 'wcii' ),
            array( &$this , 'callback_select' ),
            $design,
            'wcii_table_design_section',
            array(
                'tab' => $design,
                'id' => 'information',
                'default' => 1,
                'options' => array(
                    __( 'Not display', 'wcii' ),
                    __( 'Display', 'wcii' )
                )
            )
        );

        add_settings_field(
            'title',
            __( 'Table Title', 'wcii' ),
            array( &$this , 'callback_text' ),
            $design,
            'wcii_table_design_section',
            array(
                'tab' => $design,
                'id' => 'title',
                'default' => __( 'Credit Card Installments', 'wcii' ),
                'class' => 'regular-text'
            )
        );

        add_settings_section(
            'wcii_table_styles_section',
            __( 'Table Styles', 'wcii' ),
            '__return_false',
            $design
        );

        add_settings_field(
            'float',
            __( 'Float', 'wcii' ),
            array( &$this , 'callback_select' ),
            $design,
            'wcii_table_styles_section',
            array(
                'tab' => $design,
                'id' => 'float',
                'default' => 'none',
                'options' => array(
                    'none' => __( 'None', 'wcii' ),
                    'left' => __( 'Left', 'wcii' ),
                    'right' => __( 'Right', 'wcii' ),
                    'center' => __( 'Center', 'wcii' ),
                )
            )
        );

        add_settings_field(
            'width',
            __( 'Width', 'wcii' ),
            array( &$this , 'callback_text' ),
            $design,
            'wcii_table_styles_section',
            array(
                'tab' => $design,
                'id' => 'width',
                'default' => '100%',
                'description' => __( 'Value with %, px or em', 'wcii' )
            )
        );

        add_settings_field(
            'border',
            __( 'Border color', 'wcii' ),
            array( &$this , 'callback_color' ),
            $design,
            'wcii_table_styles_section',
            array(
                'tab' => $design,
                'id' => 'border',
                'default' => '#DDDDDD',
            )
        );

        add_settings_field(
            'odd',
            __( 'Odd background' , 'wcii' ),
            array( &$this , 'callback_color' ),
            $design,
            'wcii_table_styles_section',
            array(
                'tab' => $design,
                'id' => 'odd',
                'default' => '#F0F9E6',
            )
        );

        add_settings_field(
            'even',
            __( 'Even background', 'wcii' ),
            array( &$this , 'callback_color' ),
            $design,
            'wcii_table_styles_section',
            array(
                'tab' => $design,
                'id' => 'even',
                'default' => '#FFFFFF',
            )
        );

        add_settings_field(
            'without',
            __( 'Without interest color', 'wcii' ),
            array( &$this , 'callback_color' ),
            $design,
            'wcii_table_styles_section',
            array(
                'tab' => $design,
                'id' => 'without',
                'default' => '#006600',
            )
        );

        // Set Section.
        add_settings_section(
            'wcii_table_icons_section',
            __( '', 'wcii' ),
            '__return_false',
            $design
        );

        add_settings_field(
            'cards',
            __( 'Credit Cards', 'wcii' ),
            array( &$this , 'callback_checkbox_cards' ),
            $design,
            'wcii_table_icons_section',
            array(
                'tab' => $design,
                'id' => 'cards',
                'options' => array(
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
        register_setting( $settings, $settings, array( &$this, 'validate_input' ) );
        register_setting( $design, $design, array( &$this, 'validate_input' ) );
    }

    /**
     * Get Option.
     *
     * @param  string $tab     Tab that the option belongs
     * @param  string $id      Option ID.
     * @param  string $default Default option.
     *
     * @return array           Item options.
     */
    protected function get_option( $tab, $id, $default = '' ) {
        $options = get_option( $tab );

        if ( isset( $options[ $id ] ) )
            $default = $options[ $id ];

        return $default;
    }

    /**
     * Text field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Text field HTML.
     */
    public function callback_text( $args, $class = 'regular-text' ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = esc_html( $this->get_option( $tab, $id, $args['default'] ) );

        $html = sprintf( '<input type="text" id="%1$s" name="%2$s[%1$s]" value="%3$s" class="%4$s" />', $id, $tab, $current, $class );

        // Displays the description.
        if ( isset( $args['description'] ) )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );

        echo $html;
    }

    /**
     * Select field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Select field HTML.
     */
    public function callback_select( $args ) {
        $tab = $args['tab'];
        $id  = $args['id'];

        // Sets current option.
        $current = $this->get_option( $tab, $id, $args['default'] );

        $html = sprintf( '<select id="%1$s" name="%2$s[%1$s]">', $id, $tab );
        foreach( $args['options'] as $key => $label ) {
            $key = sanitize_title( $key );

            $html .= sprintf( '<option value="%s"%s>%s</option>', $key, selected( $current, $key, false ), $label );
        }
        $html .= '</select>';

        // Displays the description.
        if ( isset( $args['description'] ) )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );

        echo $html;
    }

    /**
     * Checkbox cards field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Checkbox cards field HTML.
     */
    public function callback_checkbox_cards( $args ) {
        $tab = $args['tab'];
        $id = $args['id'];

        $html = '';
        foreach ( $args['options'] as $key => $label ) {

            // Sets current option.
            $current = $this->get_option( $tab, $label );

            $html .= '<div class="card-item">';
            $html .= sprintf( '<input type="checkbox" id="%2$s-%4$s" name="%2$s[%1$s]" value="%4$s"%3$s />', $label, $tab, checked( $current, $key, false ), $key );
            $html .= sprintf( '<label for="%s-%s"> <div class="card-icons card-%s"></div></label>', $tab, $key, $label );
            $html .= '<div style="clear: both;"></div>';
            $html .= '</div>';
        }

        // Displays option description.
        if ( isset( $args['description'] ) )
            $html .= sprintf( '<p class="description">%s</p>', $args['description'] );

        echo $html;
    }

    /**
     * Color field callback.
     *
     * @param array $args Arguments from the option.
     *
     * @return string Color field HTML.
     */
    function callback_color( $args ) {
        $this->callback_text( $args, 'wcii-color-picker' );
    }

    /**
     * Sanitization fields callback.
     *
     * @param  string $input The unsanitized collection of options.
     *
     * @return string        The collection of sanitized values.
     */
    public function validate_input( $input ) {
        // Create our array for storing the validated options
        $output = array();

        // Loop through each of the incoming options
        foreach ( $input as $key => $value ) {

            // Check to see if the current option has a value. If so, process it.
            if ( isset( $input[ $key ] ) )
                $output[ $key ] = sanitize_text_field( $value );

        }

        return $output;
    }
}
