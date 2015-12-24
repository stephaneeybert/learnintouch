(function() {

  CKEDITOR.plugins.lexiconClear = {
  };

  var plugin = CKEDITOR.plugins.lexiconClear;

  CKEDITOR.plugins.add('lexiconClear', {
    lang: [CKEDITOR.config.currentLanguage],
    init: function(editor) {
      var pluginName = 'lexiconClear';
      editor.ui.addButton('LexiconClear', {
        title : editor.lang.lexiconClear.lexiconClear.toolbar_button,
        command : 'removeFormat',
        icon: CKEDITOR.plugins.getPath(pluginName) + 'images/lexiconClearL.png'
      });
    }
  });

})();

