( function(){

  var imageTabName = 'imageTab';

  var parentEditor = window.parent.CKEDITOR;

  var okListener = function(event) {  
    var imageUrl = this._.editor.config.imageUrl;
    var htmlImg = "<img src='" + imageUrl + '/' + this.getContentElement(imageTabName, 'uploadedImageFile').getValue() + "' border='0' href='' title=''>";
    this._.editor.insertHtml(htmlImg);  
    parentEditor.dialog.getCurrent().removeListener("ok", okListener);  
  };

  // The name of the dialog must be the same as the one specified in the plugin file
  CKEDITOR.dialog.add('uploadImageDialog', function( editor ) {
    return {
    title: editor.lang.imageUpload.title,
    resizable: CKEDITOR.DIALOG_RESIZE_NONE,
    minWidth: 500,
    minHeight: 100,
    onOk: okListener,
    onCancel: '',
    onLoad: '',
    onShow: function() {
      // Get the element currently selected by the user
      var selectedContent = this.getParentEditor().getSelection().getSelectedElement();
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
      id: imageTabName,
      label: '',
      title: '',
      accessKey: '',
      elements: [
        {
        type: 'hbox',
        widths : ['50%', '50%'],
        children: [
        {
        type: 'text',
        id: 'uploadedImageFile',
        label: editor.lang.imageUpload.label_image,
        labelLayout: 'horizontal',
        size: 50,
        'default': '', // As a reserved keyword, default needs to be placed within quotes
        setup: function(element) {
          // Preset the element value with the src attribute of the content selected by the user
          this.setValue(element.getAttribute('src'));
        },
        validate : function() {
          if (this.getValue().length == 0) {
            return editor.lang.imageUpload.warning_choose_image;
          }
        }
        },
        {
        type: 'button',
        id: 'browseButton',
        label: editor.lang.common.browseServer,
        filebrowser : {
          action: 'Browse', // Call the browse popup window
          onSelect : function(fileUrl, options) {
//            alert('The selected file URL is "' + fileUrl + '"');
//            // Some options can be passed by the child popup window
//            for (var key in options) {
//              alert('options["' + key + '"]' + ' = ' + options[key]);
//            }
            var imageName = basename(fileUrl, '');
            var dialog = this.getDialog();
            dialog.getContentElement(imageTabName, 'uploadedImageFile').setValue(imageName);

            // Do not call the built-in onSelect command 
            return false;
            }
          }
        }
        ]
        }
      ]
      },
        {
          id : 'advanced',
          label : editor.lang.common.advancedTab,
          elements :
          [
            {
              type : 'hbox',
              widths : [ '50%', '25%', '25%' ],
              children :
              [
                {
                  type : 'text',
                  id : 'linkId',
                  label : editor.lang.common.id,
                  setup : function( type, element )
                  {
                    if ( type == IMAGE )
                      this.setValue( element.getAttribute( 'id' ) );
                  },
                  commit : function( type, element )
                  {
                    if ( type == IMAGE )
                    {
                      if ( this.getValue() || this.isChanged() )
                        element.setAttribute( 'id', this.getValue() );
                    }
                  }
                },
                {
                  id : 'cmbLangDir',
                  type : 'select',
                  style : 'width : 100px;',
                  label : editor.lang.common.langDir,
                  'default' : '',
                  items :
                  [
                    [ editor.lang.common.notSet, '' ],
                    [ editor.lang.common.langDirLtr, 'ltr' ],
                    [ editor.lang.common.langDirRtl, 'rtl' ]
                  ],
                  setup : function( type, element )
                  {
                    if ( type == IMAGE )
                      this.setValue( element.getAttribute( 'dir' ) );
                  },
                  commit : function( type, element )
                  {
                    if ( type == IMAGE )
                    {
                      if ( this.getValue() || this.isChanged() )
                        element.setAttribute( 'dir', this.getValue() );
                    }
                  }
                },
                {
                  type : 'text',
                  id : 'txtLangCode',
                  label : editor.lang.common.langCode,
                  'default' : '',
                  setup : function( type, element )
                  {
                    if ( type == IMAGE )
                      this.setValue( element.getAttribute( 'lang' ) );
                  },
                  commit : function( type, element )
                  {
                    if ( type == IMAGE )
                    {
                      if ( this.getValue() || this.isChanged() )
                        element.setAttribute( 'lang', this.getValue() );
                    }
                  }
                }
              ]
            },
            {
              type : 'text',
              id : 'txtGenLongDescr',
              label : editor.lang.common.longDescr,
              setup : function( type, element )
              {
                if ( type == IMAGE )
                  this.setValue( element.getAttribute( 'longDesc' ) );
              },
              commit : function( type, element )
              {
                if ( type == IMAGE )
                {
                  if ( this.getValue() || this.isChanged() )
                    element.setAttribute( 'longDesc', this.getValue() );
                }
              }
            },
            {
              type : 'hbox',
              widths : [ '50%', '50%' ],
              children :
              [
                {
                  type : 'text',
                  id : 'txtGenClass',
                  label : editor.lang.common.cssClass,
                  'default' : '',
                  setup : function( type, element )
                  {
                    if ( type == IMAGE )
                      this.setValue( element.getAttribute( 'class' ) );
                  },
                  commit : function( type, element )
                  {
                    if ( type == IMAGE )
                    {
                      if ( this.getValue() || this.isChanged() )
                        element.setAttribute( 'class', this.getValue() );
                    }
                  }
                },
                {
                  type : 'text',
                  id : 'txtGenTitle',
                  label : editor.lang.common.advisoryTitle,
                  'default' : '',
                  onChange : function()
                  {
                    updatePreview( this.getDialog() );
                  },
                  setup : function( type, element )
                  {
                    if ( type == IMAGE )
                      this.setValue( element.getAttribute( 'title' ) );
                  },
                  commit : function( type, element )
                  {
                    if ( type == IMAGE )
                    {
                      if ( this.getValue() || this.isChanged() )
                        element.setAttribute( 'title', this.getValue() );
                    }
                    else if ( type == PREVIEW )
                    {
                      element.setAttribute( 'title', this.getValue() );
                    }
                    else if ( type == CLEANUP )
                    {
                      element.removeAttribute( 'title' );
                    }
                  }
                }
              ]
            },
            {
              type : 'text',
              id : 'txtdlgGenStyle',
              label : editor.lang.common.cssStyle,
              'default' : '',
              setup : function( type, element )
              {
                if ( type == IMAGE )
                {
                  var genStyle = element.getAttribute( 'style' );
                  if ( !genStyle && element.$.style.cssText )
                    genStyle = element.$.style.cssText;
                  this.setValue( genStyle );

                  var height = element.$.style.height,
                    width = element.$.style.width,
                    aMatchH  = ( height ? height : '' ).match( regexGetSize ),
                    aMatchW  = ( width ? width : '').match( regexGetSize );

                  this.attributesInStyle =
                  {
                    height : !!aMatchH,
                    width : !!aMatchW
                  };
                }
              },
              onChange : function ()
              {
                commitInternally.call( this,
                  [ 'info:cmbFloat', 'info:cmbAlign',
                    'info:txtVSpace', 'info:txtHSpace',
                    'info:txtBorder',
                    'info:txtWidth', 'info:txtHeight' ] );
                updatePreview( this );
              },
              commit : function( type, element )
              {
                if ( type == IMAGE && ( this.getValue() || this.isChanged() ) )
                {
                  element.setAttribute( 'style', this.getValue() );
                }
              }
            }
          ]
        }

    ]
    };
  });

})();

