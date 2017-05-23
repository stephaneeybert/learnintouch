( function(){

  var linkTabName = 'linkTab';
  var selectedLinkFieldName = 'selectedLink';
  var selectedTargetFieldName = 'selectedTarget';

  var parentEditor = window.parent.CKEDITOR;

  var getSelectedContent = function(editor) {
    var selectedContent = '';
    var selection = editor.getSelection();
    if (selection.getType() == CKEDITOR.SELECTION_ELEMENT) {
      var selectedContent = selection.getSelectedElement().$.outerHTML;
    } else if (selection.getType() == CKEDITOR.SELECTION_TEXT) {
      if (CKEDITOR.env.ie) {
        selection.unlock(true);
        if (selection.getSelection) {
          selectedContent = selection.getSelection().text;
        } else if (selection.getSelectedText) {
          selectedContent = selection.getSelectedText();
        } else if (selection.getNative) {
          selectedContent = selection.getNative().createRange().text;
        }
      } else {
        selectedContent = selection.getNative();
      }
    }
    return(selectedContent);
  }

  var okListener = function(event) {  
    var linkUrl = this.getContentElement(linkTabName, selectedLinkFieldName).getValue();
    linkUrl = getRelativeUrl(linkUrl);
    var target = this.getContentElement(linkTabName, selectedTargetFieldName).getValue();
    // Retrieve the saved selected content
    var selectedContent = this._.selectedContent;
    this._.editor.insertHtml('<a href="' + linkUrl + '" target="' + target + '">' + selectedContent + '</a>');
    parentEditor.dialog.getCurrent().removeListener("ok", okListener);  
  };

  // The name of the dialog must be the same as the one specified in the plugin file
  CKEDITOR.dialog.add('internalLinkDialog', function( editor ) {
    return {
    title: editor.lang.internalLink.internalLink.title,
    resizable: CKEDITOR.DIALOG_RESIZE_NONE,
    minWidth: 500,
    minHeight: 100,
    onOk: okListener,
    onCancel: '',
    onLoad: '',
    onShow: function() {
      // Get the element currently selected by the user
      var editor = this.getParentEditor();
      var selectedContent = getSelectedContent(editor);
     // Save the selected content as it is later needed in the ok listener
     // Otherwise, on IE7, it cannot be retrieved again from the ok listener
      this._.selectedContent = selectedContent;
      // Call the setup function of all the dialog elements
      this.setupContent(selectedContent);
    },
    onHide: '',
    buttons: [
      CKEDITOR.dialog.okButton, 
      CKEDITOR.dialog.cancelButton
    ],
    contents: [
      {
      id: linkTabName,
      label: '',
      title: '',
      accessKey: '',
      elements: [
        {
        type: 'vbox',
        width : '75%',
        children: [
        {
        type: 'hbox',
        width : '100%',
        children: [
        {
        type: 'text',
        id: selectedLinkFieldName,
        label: editor.lang.internalLink.internalLink.label_link,
        labelLayout: 'horizontal',
        'default': '', // As a reserved keyword, default needs to be placed within quotes
        setup: function(selectedMarkup) {
          // Make sure some text has been selected
          if (selectedMarkup == '') {
            alert(CKEDITOR.tools.htmlEncode(editor.lang.internalLink.internalLink.warning_select_text));
          }
          // Preset the element value with the src attribute of the content selected by the user
          if (selectedMarkup.getAttribute && selectedMarkup.getAttribute('href')) {
            this.setValue(selectedMarkup.getAttribute('href'));
          }
        },
        validate : function() {
          if (this.getValue().length == 0) {
            return editor.lang.internalLink.internalLink.warning_choose_link;
          }
        }
        },
        {
        type: 'button',
        id: 'browseLink',
        label: editor.lang.internalLink.internalLink.button_browse,
        filebrowser : {
          action : 'Browse',
          target: linkTabName + ':' + selectedLinkFieldName,
          url: CKEDITOR.config.linkSelectUrl,
          onSelect : function(linkUrl, newWindow) {
            var dialog = this.getDialog();
            dialog.getContentElement(linkTabName, selectedLinkFieldName).setValue(linkUrl);
            if (newWindow) {
              dialog.getContentElement(linkTabName, selectedTargetFieldName).setValue('_blank');
            } else {
              dialog.getContentElement(linkTabName, selectedTargetFieldName).setValue('_self');
            }
            // Do not call the built-in onSelect command 
            return false;
          }
        }
        }
        ]
        },
        {
        type: 'select',
        id: selectedTargetFieldName,
        label: editor.lang.internalLink.internalLink.label_target,
        items: [
            [ editor.lang.internalLink.internalLink.label_target_self, '_self' ],
            [ editor.lang.internalLink.internalLink.label_target_new, '_blank' ]
          ]
        }
        ]
        }
      ]
      }
    ]
    };
  });

})();

