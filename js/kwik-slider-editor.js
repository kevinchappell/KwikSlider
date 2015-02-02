'use strict' ;
(function() {
  var ajaxurl = window.ajaxurl,
      tinymce = window.tinymce;

  tinymce.create('tinymce.plugins.add_kwik_slider', {
    init: function(editor, url) {
      editor.addCommand('add_kwik_slider', function() {
        editor.windowManager.open({
          file: ajaxurl + '?action=add_kwik_slider',
          width: 350 + parseInt(editor.getLang('mytest.delta_width', 0)),
          height: 250 + parseInt(editor.getLang('mytest.delta_height', 0)),
          inline: 1
        }, {
          plugin_url: url
        });
      });
      editor.addButton('add_kwik_slider', {
        classes: 'btn widget dashicons-slides',
        title: 'Add a Slider to this post',
        cmd: 'add_kwik_slider'
      });
    },
  });
  tinymce.PluginManager.add('add_kwik_slider', tinymce.plugins.add_kwik_slider);
})();
