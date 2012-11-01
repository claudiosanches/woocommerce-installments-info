<?php
include '../../../../wp-blog-header.php';
?><!DOCTYPE html>
<html dir="ltr" lang="en">
<head>
<meta charset="UTF-8" />
<title><?php _e( 'Credit Card Interest Table', 'wcccit' ) ?></title>
<script src="../../../../wp-includes/js/jquery/jquery.js" type="text/javascript"></script>
<script src="../../../../wp-includes/js/tinymce/tiny_mce_popup.js" type="text/javascript"></script>
<script>
jQuery(document).ready(function($) {

    var wcccit = {
        e: '',
        init: function(e) {
            wcccit.e = e;
            tinyMCEPopup.resizeToInnerSize();
        },
        insert: function createGalleryShortcode(e) {

            var wcccitPrice            = $('#wcccit-price').val();
            var wcccitParcelMaximum    = $('#wcccit-parcel-maximum').val();
            var wcccitParcelMinimum    = $('#wcccit-parcel-minimum').val();
            var wcccitIota             = $('#wcccit-iota').val();
            var wcccitWithoutIinterest = $('#wcccit-without-interest').val();
            var wcccitInterest         = $('#wcccit-interest').val();
            var wcccitCalculationType  = $('#wcccit-calculation-type').val();

            var output = '[wcccit';

            if (wcccitPrice) {
                output += ' price="' + wcccitPrice + '"';
            }

            if (wcccitParcelMaximum) {
                output += ' parcel_maximum="' + wcccitParcelMaximum + '"';
            }

            if (wcccitParcelMinimum) {
                output += ' parcel_minimum="' + wcccitParcelMinimum + '"';
            }

            if (wcccitIota) {
                output += ' iota="' + wcccitIota + '"';
            }

            if (wcccitWithoutIinterest) {
                output += ' without_interest="' + wcccitWithoutIinterest + '"';
            }

            if (wcccitInterest) {
                output += ' interest="' + wcccitInterest + '"';
            }

            if (wcccitInterest) {
                output += ' calculation_type="' + wcccitInterest + '"';
            }

            output += ']';

            tinyMCEPopup.execCommand('mceReplaceContent', false, output);

            tinyMCEPopup.close();
        }
    }
    tinyMCEPopup.onInit.add(wcccit.init, wcccit);

    $('#wcccit-form').on('submit', function(e) {
        wcccit.insert(wcccit.e);
    });

});
</script>
</head>
<body>
    <form id="wcccit-form" action="#">
        <div class="column">
            <p>
                <label for="wcccit-price"><?php _e( 'Price', 'wcccit' ) ?>:</label><br/>
                <input id="wcccit-price" type="text" value="" />
            </p>
            <p>
                <label for="wcccit-parcel-maximum"><?php _e( 'Number of parcels', 'wcccit' ); ?>:</label><br/>
                <input id="wcccit-parcel-maximum" type="text" value="" />
            </p>
            <p>
                <label for="wcccit-parcel-minimum"><?php _e( 'Parcel minimum', 'wcccit' ); ?>:</label><br/>
                <input id="wcccit-parcel-minimum" type="text" value="" />
            </p>
            <p>
                <label for="wcccit-iota"><?php _e( 'iota', 'wcccit' ); ?>:</label><br/>
                <input id="wcccit-iota" type="text" value="" />
            </p>
        </div>

        <div class="column" style="margin-left: 20px;">
            <p>
                <label for="wcccit-without-interest"><?php _e( 'Parcels without interest', 'wcccit' ); ?>:</label><br/>
                <input id="wcccit-without-interest" type="text" value="" />
            </p>
            <p>
                <label for="wcccit-interest"><?php _e( 'Interest', 'wcccit' ); ?>:</label><br/>
                <input id="wcccit-interest" type="text" value="" />
            </p>
            <p>
                <label for="wcccit-calculation-type"><?php _e( 'Calculation type', 'wcccit' ); ?>:</label><br/>
                <select id="wcccit-calculation-type">
                    <option value="0"><?php _e( 'Amortization schedule', 'wcccit' ); ?></option>
                    <option value="1"><?php _e( 'Simple interest', 'wcccit' ); ?></option>
                </select>
            </p>
        </div>
        <br style="clear: both;" />
        <p>
            <input type="submit" id="insert" value="Add" />
        </p>
    </form>
</body>
</html>
