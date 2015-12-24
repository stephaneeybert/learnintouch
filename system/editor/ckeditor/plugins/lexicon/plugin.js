(function() {

  CKEDITOR.plugins.lexicon = {
  };

  var plugin = CKEDITOR.plugins.lexicon;

  var commandFunction = {
    exec : function(editor) {
      // Save the selected content as it is later needed in the ok listener
      // Otherwise, on IE7, it cannot be retrieved again from the connector
      editor._.selectedContent = getSelectedContent(editor);
      window.open(editor.config.lexiconSelectUrl, null, "dialogWidth:700px;dialogHeight:500px;center:yes;resizable:yes;help:no;");
    }
  }

  var getSelectedContent = function(editor) {
    var selectedContent = '';
    var selection = editor.getSelection();
    if (selection.getType() == CKEDITOR.SELECTION_ELEMENT) {
      var selectedContent = selection.getSelectedElement().$.outerHTML;
    } else if (selection.getType() == CKEDITOR.SELECTION_TEXT) {
      if (CKEDITOR.env.ie) {
        selection.unlock(true);
        selectedContent = selection.getNative().createRange().text;
      } else {
        selectedContent = selection.getNative();
      }
    }
    return(selectedContent);
  }  

  CKEDITOR.plugins.add('lexicon', {
    lang : [CKEDITOR.config.currentLanguage],
    requires : ['iframedialog'],
    init: function(editor) {
      var pluginName = 'lexicon';
      var commandName = 'insertLexiconEntry';
      editor.addCommand(commandName, commandFunction);
      editor.ui.addButton('Lexicon', {
        title : editor.lang.lexicon.lexicon.toolbar_button,
        command : commandName,
        icon: CKEDITOR.plugins.getPath(pluginName) + 'images/lexiconL.png'
      });
    }
  });

})();

