<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly.
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<title><?php _e( 'Installments Info', 'wcii' ) ?></title>
<script src="<?php echo includes_url( 'js/jquery/jquery.js' ); ?>" type="text/javascript"></script>
<script src="<?php echo includes_url( 'js/tinymce/tiny_mce_popup.js' ); ?>" type="text/javascript"></script>
<script src="<?php echo WC_INSTALLMENTS_INFO_URL . 'assets/js/tinymce.wcii.dialog.js'; ?>" type="text/javascript"></script>
</head>
<body>
    <form id="wcii-form" action="#">
        <div class="column">
            <p>
                <label for="wcii-price"><?php _e( 'Price', 'wcii' ) ?>:</label><br/>
                <input id="wcii-price" type="text" value="" />
            </p>
            <p>
                <label for="wcii-installment-maximum"><?php _e( 'Number of installments', 'wcii' ); ?>:</label><br/>
                <input id="wcii-installment-maximum" type="text" value="" />
            </p>
            <p>
                <label for="wcii-installment-minimum"><?php _e( 'Installment minimum', 'wcii' ); ?>:</label><br/>
                <input id="wcii-installment-minimum" type="text" value="" />
            </p>
            <p>
                <label for="wcii-iota"><?php _e( 'iota', 'wcii' ); ?>:</label><br/>
                <input id="wcii-iota" type="text" value="" />
            </p>
        </div>

        <div class="column" style="margin-left: 20px;">
            <p>
                <label for="wcii-without-interest"><?php _e( 'Installments without interest', 'wcii' ); ?>:</label><br/>
                <input id="wcii-without-interest" type="text" value="" />
            </p>
            <p>
                <label for="wcii-interest"><?php _e( 'Interest', 'wcii' ); ?>:</label><br/>
                <input id="wcii-interest" type="text" value="" />
            </p>
            <p>
                <label for="wcii-calculation-type"><?php _e( 'Calculation type', 'wcii' ); ?>:</label><br/>
                <select id="wcii-calculation-type">
                    <option value="0"><?php _e( 'Amortization schedule', 'wcii' ); ?></option>
                    <option value="1"><?php _e( 'Simple interest', 'wcii' ); ?></option>
                </select>
            </p>
        </div>
        <br style="clear: both;" />
        <p>
            <input type="submit" id="insert" value="<?php _e( 'Insert', 'wcii' ); ?>" />
        </p>
    </form>
</body>
</html>
