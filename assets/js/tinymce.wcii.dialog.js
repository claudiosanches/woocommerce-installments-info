jQuery(document).ready(function($) {

    var wcii = {
        e: '',
        init: function(e) {
            wcii.e = e;
            tinyMCEPopup.resizeToInnerSize();
        },
        insert: function createGalleryShortcode(e) {

            var wciiPrice              = $('#wcii-price').val();
            var wciiInstallmentMaximum = $('#wcii-installment-maximum').val();
            var wciiInstallmentMinimum = $('#wcii-installment-minimum').val();
            var wciiIota               = $('#wcii-iota').val();
            var wciiWithoutIinterest   = $('#wcii-without-interest').val();
            var wciiInterest           = $('#wcii-interest').val();
            var wciiCalculationType    = $('#wcii-calculation-type').val();

            var output = '[wcii';

            if (wciiPrice) {
                output += ' price="' + wciiPrice + '"';
            }

            if (wciiInstallmentMaximum) {
                output += ' installment_maximum="' + wciiInstallmentMaximum + '"';
            }

            if (wciiInstallmentMinimum) {
                output += ' installment_minimum="' + wciiInstallmentMinimum + '"';
            }

            if (wciiIota) {
                output += ' iota="' + wciiIota + '"';
            }

            if (wciiWithoutIinterest) {
                output += ' without_interest="' + wciiWithoutIinterest + '"';
            }

            if (wciiInterest) {
                output += ' interest="' + wciiInterest + '"';
            }

            if (wciiInterest) {
                output += ' calculation_type="' + wciiInterest + '"';
            }

            output += ']';

            tinyMCEPopup.execCommand('mceReplaceContent', false, output);

            tinyMCEPopup.close();
        }
    };

    tinyMCEPopup.onInit.add(wcii.init, wcii);

    $('#wcii-form').on('submit', function(e) {
        wcii.insert(wcii.e);
    });
});
