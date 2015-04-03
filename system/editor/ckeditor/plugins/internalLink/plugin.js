(function() {

  CKEDITOR.plugins.internalLink = {
  };

  var plugin = CKEDITOR.plugins.internalLink;

  CKEDITOR.plugins.add('internalLink', {
    lang: [CKEDITOR.config.currentLanguage],
    requires: ['iframedialog'],
    init: function(editor) {
      var pluginName = 'internalLink';
      var commandName = 'insertInternalLink';
      var dialogName = 'internalLinkDialog';
      CKEDITOR.dialog.add(dialogName, this.path + 'dialogs/internalLink.js');
      editor.addCommand(commandName, new CKEDITOR.dialogCommand(dialogName));
      editor.ui.addButton('InternalLink', {
        label : editor.lang.internalLink.toolbar_button,
        command : commandName,
        icon: CKEDITOR.plugins.getPath(pluginName) + 'images/internalLink.png'
      });
    }
  });

})();

