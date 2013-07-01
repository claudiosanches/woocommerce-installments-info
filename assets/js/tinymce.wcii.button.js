(function() {
    tinymce.create('tinymce.plugins.wcii', {
        init: function(ed, url) {
            ed.addCommand('wciiCommand', function() {
                ed.windowManager.open({
                    file: ajaxurl + '?action=wcii_tinymce_dialog',
                    width: 380 + parseInt(ed.getLang('wcii.delta_width', 0), 10),
                    height: 250 + parseInt(ed.getLang('wcii.delta_height', 0), 10),
                    inline: 1
                }, {
                    plugin_url: url
                });
            });
            ed.addButton('wcii', {
                title: 'Installments Info',
                image: url + '/../images/tinymce-wcii-button.png',
                cmd: 'wciiCommand'
            });
        },
        getInfo: function() {
            return {
                longname: "Installments Info Shortcode",
                author: 'Claudio Sanches',
                authorurl: 'http://claudiosmweb.com/',
                infourl: 'https://github.com/claudiosmweb/woocommerce-installments-info',
                version: "2.0.0"
            };
        }
    });

    tinymce.PluginManager.add('wcii', tinymce.plugins.wcii);
})();
