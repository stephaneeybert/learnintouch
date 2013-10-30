(function() {

  CKEDITOR.plugins.ajaxSave = {
  };

  var plugin = CKEDITOR.plugins.ajaxSave;

  function getContent(editor) {
    var content = editor.getData();
    return(content);
  }

  CKEDITOR.plugins.add('ajaxSave', {
    lang : [CKEDITOR.config.currentLanguage],
    init : function(editor) {
      var pluginName = 'ajaxSave';
      var commandName = 'saveContent';
      editor.addCommand(commandName, {
        exec : function(editor) {
          var content = getContent(editor);
          saveEditorContent(editor.name, content);
        },
        async : true
      });
      editor.ui.addButton('AjaxSave', {
        label : editor.lang.ajaxSave.toolbar_button,
        command : commandName,
        icon : CKEDITOR.plugins.getPath(pluginName) + 'images/ajaxSave.png'
      });
    }
  });

})();

