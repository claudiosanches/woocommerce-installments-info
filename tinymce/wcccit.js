(function() {
    tinymce.create('tinymce.plugins.wcccit', {
        init : function(ed, url) {
            ed.addCommand('wcccitCommand', function() {
                ed.windowManager.open({
                    file : url + '/dialog.php',
                    width : 380 + parseInt(ed.getLang('wcccit.delta_width', 0)),
                    height : 250 + parseInt(ed.getLang('wcccit.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });
            ed.addButton('wcccit', {
                title : 'Credit Card Interest Table',
                image : url + '/wcccit.png',
                cmd : 'wcccitCommand'
            });
        },
        getInfo : function() {
            return {
                longname : "Credit Card Interest Table Shortcode",
                author : 'Claudio Sanches',
                authorurl : 'http://claudiosmweb.com/',
                infourl : 'http://claudiosmweb.com/',
                version : "1.0"
            };
        }
    });
    tinymce.PluginManager.add('wcccit', tinymce.plugins.wcccit);
})();
