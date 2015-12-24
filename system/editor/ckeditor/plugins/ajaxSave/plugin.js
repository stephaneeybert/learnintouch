(function() {

  CKEDITOR.plugins.ajaxSave = {
  };

  var plugin = CKEDITOR.plugins.ajaxSave;

  CKEDITOR.plugins.add('ajaxSave', {
    lang : [CKEDITOR.config.currentLanguage],
    init : function(editor) {
      var pluginName = 'ajaxSave';
      var commandName = 'saveContent';
      editor.addCommand(commandName, {
        exec : function(editor) {
          console.log(editor);
          saveEditorContent(editor.name, editor.document.getBody().getHtml());
        },
        async : true
      });
      editor.ui.addButton('AjaxSave', {
        title : editor.lang.ajaxSave.ajaxSave.toolbar_button,
        command : commandName,
        icon : CKEDITOR.plugins.getPath(pluginName) + 'images/ajaxSave.png'
      });
    }
  });

})();

