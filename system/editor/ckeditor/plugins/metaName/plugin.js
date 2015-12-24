(function() {

	CKEDITOR.plugins.add('metaName', {
    lang : [CKEDITOR.config.currentLanguage],
		requires : ['richcombo'],
		init : function(editor) {
      var metaNames = editor.config.metaNamesJs;
 
		  editor.ui.addRichCombo('MetaName', {
        label : editor.lang.metaName.metaName.label,
        title : editor.lang.metaName.metaName.title,
        voiceLabel : editor.lang.metaName.metaName.label,
        className : 'cke_format',
        multiSelect : false,
        panel : {
          css : [ editor.config.contentsCss, CKEDITOR.getUrl(editor.skinPath + 'editor.css') ],
          voiceLabel : editor.lang.panelVoiceLabel
        },
        init : function() {
          for (var metaName in metaNames){
            this.add(metaNames[metaName][1], metaNames[metaName][0], metaNames[metaName][0]);
          }
        },
        onClick : function(value) {
          editor.focus();
					editor.fire( 'saveSnapshot' );
          editor.insertHtml(value);
					editor.fire( 'saveSnapshot' );
        }
			});
		}
	});

})();

