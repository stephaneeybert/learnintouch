(function() {

  CKEDITOR.plugins.imageUpload = {
  };

  var plugin = CKEDITOR.plugins.imageUpload;

  CKEDITOR.plugins.add('imageUpload', {
    lang : [CKEDITOR.config.currentLanguage],
    requires : ['iframedialog'],
    init: function(editor) {
      var pluginName = 'imageUpload';
      var commandName = 'uploadImage';
      var dialogName = 'uploadImageDialog';
      CKEDITOR.dialog.add(dialogName, this.path + 'dialogs/imageUpload.js');
      editor.addCommand(commandName, new CKEDITOR.dialogCommand(dialogName));
      editor.ui.addButton('ImageUpload', {
        label : editor.lang.common.image,
        command : commandName,
        icon: CKEDITOR.plugins.getPath(pluginName) + 'images/imageUpload.png'
      });
    }
  });

})();

