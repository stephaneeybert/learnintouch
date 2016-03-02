<?php

class CKEditorUtils {

  var $editorName;
  var $content;
  var $standardToolbar;
  var $reducedToolbar;
  var $bareToolbar;
  var $serverPing;
  var $languageCode;
  var $customTagButton;
  var $imageButton;
  var $flashButton;
  var $ajaxSave;
  var $lexicon;
  var $metaName;
  var $metaNamesJs;
  var $height;
  var $imagePath;
  var $imageUrl;
  var $imageBrowserUploadUrl;

  var $languageUtils;
  var $commonUtils;

  function CKEditorUtils() {
  }

  function load() {
    $this->serverPing = $this->commonUtils->renderServerPing();

    $this->languageCode = $this->languageUtils->getCurrentAdminLanguageCode();
  }

  function withImageButton() {
    $this->imageButton = true;
  }

  function withFlashButton() {
    $this->flashButton = true;
  }

  function withAjaxSave() {
    $this->ajaxSave = true;
  }

  function withLexicon() {
    $this->lexicon = true;
  }

  function withMetaNames() {
    $this->metaName = true;
  }

  function setMetaNamesJs($metaNamesJs) {
    $this->metaNamesJs = $metaNamesJs;
  }

  function setHeight($height) {
    $this->height = $height;
  }

  function setImagePath($imagePath) {
    $this->imagePath = $imagePath;
  }

  function setImageUrl($imageUrl) {
    global $gHomeUrl;

    $imageUrl = str_replace($gHomeUrl, '', $imageUrl);

    $this->imageUrl = $imageUrl;
  }

  function setImageBrowserUploadUrl($imageBrowserUploadUrl) {
    $this->imageBrowserUploadUrl = $imageBrowserUploadUrl;
  }

  function withStandardToolbar() {
    $this->standardToolbar =  true;
  }

  function withReducedToolbar() {
    $this->reducedToolbar =  true;
  }

  function withBareToolbar() {
    $this->bareToolbar =  true;
  }

  function renderToolbar() {
    if ($this->imageButton) {
      $imageButton = "['ImageUpload'],";
    } else {
      $imageButton = '';
    }

    if ($this->flashButton) {
      $flashButton = "['Flash'],";
    } else {
      $flashButton = '';
    }

    if ($this->ajaxSave) {
      $saveButton = "['AjaxSave'],";
    } else {
      $saveButton = "['Save'],";
    }

    if ($this->lexicon) {
      $lexiconButton = "['Lexicon', 'LexiconClear'],";
    } else {
      $lexiconButton = '';
    }

    if ($this->metaName) {
      $metaNameButton = "['MetaName'],";
    } else {
      $metaNameButton = '';
    }

    if ($this->standardToolbar) {
      $str = <<<HEREDOC
    toolbar: [
      $saveButton
      ['Preview', 'Print', 'Maximize', 'ShowBlocks'],
      ['Cut', 'Copy', 'PasteText', 'Undo', 'Redo'],
      $imageButton
      $flashButton
      $lexiconButton
      ['InternalLink', 'Link', 'Unlink', 'Anchor'],
      $metaNameButton
      ['TextColor', 'BGColor'],
      ['Bold', 'Italic', 'Underline', 'Strike'],
      ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
      ['Subscript', 'Superscript'],
      '/',
      ['Table', 'HorizontalRule', 'Smiley', 'SpecialChar'],
      ['Find', 'Replace', 'SelectAll', 'RemoveFormat', '-', 'SpellChecker', 'Scayt'],
      ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
      ['Styles', 'Format', 'Font', 'FontSize'],
      ['Source', 'About']
    ]
HEREDOC;
    } else if ($this->reducedToolbar) {
      $str = <<<HEREDOC
    toolbar: [
      $saveButton
      ['Preview'],
      ['Cut', 'Copy', 'PasteText', 'Undo', 'Redo'],
      $imageButton
      $flashButton
      $lexiconButton
      ['InternalLink', 'Link', 'Unlink', 'Anchor'],
      $metaNameButton
      ['TextColor', 'BGColor'],
      ['Bold', 'Italic', 'Underline'],
      ['JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'],
      ['Subscript', 'Superscript'],
      '/',
      ['HorizontalRule', 'Smiley', 'SpecialChar'],
      ['Find', 'Replace', 'SelectAll', 'RemoveFormat', '-', 'SpellChecker', 'Scayt'],
      ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', 'Blockquote'],
      ['Source', 'About']
    ]
HEREDOC;
    } else if ($this->bareToolbar) {
      $str = <<<HEREDOC
    toolbar: [
      $saveButton
      ['Preview'],
      $flashButton
      $lexiconButton
      ['InternalLink', 'Link', 'Unlink', 'Anchor'],
      $metaNameButton
      ['Source', 'About']
    ]
HEREDOC;
    } else {
      $str = '';
    }

    return($str);
  }

  function render() {
    global $gJsUrl;
    global $gSystemUrl;

    $languageCode = $this->languageCode;

    $linkSelectUrl = $gSystemUrl . '/editor/ckeditor/connector/internal_link.php';
    $lexiconSelectUrl = $gSystemUrl . '/editor/ckeditor/connector/lexicon.php';

    $str = <<<HEREDOC
<script type='text/javascript' src='$gJsUrl/editor/ckeditor_4.5.6_full/ckeditor.js'></script>
<script type="text/javascript">
  window.CKEDITOR_BASEPATH = '$gJsUrl/editor/ckeditor_4.5.6_full/';
</script>
HEREDOC;

    $str .= <<<HEREDOC
<script type="text/javascript">
CKEDITOR.config.basePath = '$gJsUrl/editor/ckeditor_4.5.6_full/';
CKEDITOR.config.tabSpaces = '10';
CKEDITOR.config.currentLanguage = '$languageCode';
CKEDITOR.config.linkSelectUrl = '$linkSelectUrl';
CKEDITOR.config.lexiconSelectUrl = '$lexiconSelectUrl';
CKEDITOR.config.font_names = 'Chunk Five Regular/ChunkFiveRegular;' + CKEDITOR.config.font_names;
CKEDITOR.config.removePlugins = 'image';
CKEDITOR.config.allowedContent = true;

// When opening a dialog, its "definition" is created for it, for each editor instance. The "dialogDefinition" event is then fired. We should use this event to make customizations to the definition of existing dialogs.
CKEDITOR.on('dialogDefinition', function(ev) {
  // Take the dialog name and its definition from the event data.
  var dialogName = ev.data.name;
  var dialogDefinition = ev.data.definition;

  if (dialogName == 'link') {
    // Remove some tabs
    dialogDefinition.removeContents('target');
    dialogDefinition.removeContents('advanced');

    // Get a reference to the "Info" tab
    var infoTab = dialogDefinition.getContents('info');

    // Remove the "Link Type" combo and the "Browser Server" button from the "info" tab.
    infoTab.remove('browse');
  }

  if (dialogName == 'image') {
    // Remove some tabs
    dialogDefinition.removeContents('Link');
    dialogDefinition.removeContents('advanced');
  }

  if ( dialogName == 'flash' ) {
    dialogDefinition.removeContents('advanced');
  }

});

</script>
HEREDOC;

    $str .= $this->serverPing;

    return($str);
  }

  function renderInstance($editorName, $content) {
    $this->editorName = $editorName;
    $this->content = $content;

    $languageCode = $this->languageCode;

    $imagePath = $this->imagePath;
    $imageUrl = $this->imageUrl;

    $height = $this->height;

    if ($this->imageBrowserUploadUrl) {
      $strFilebrowserUploadUrl = "filebrowserBrowseUrl: '" . $this->imageBrowserUploadUrl . "',";
    } else {
      $strFilebrowserUploadUrl = '';
    }

    $toolbar = $this->renderToolbar();

    $str = <<<HEREDOC
<textarea cols="80" id="$editorName" name="$editorName" rows="10">$content</textarea>
<script type="text/javascript">
  CKEDITOR.replace('$editorName', {
    language: '$languageCode',
    enterMode: CKEDITOR.ENTER_BR,
    height: '$height',
    extraAllowedContent: 'div(*)[*]; span(*)[*]; img[alt,src]',
    extraPlugins: 'imageUpload,ajaxSave,lexicon,lexiconClear,internalLink,metaName',
    $strFilebrowserUploadUrl
    $toolbar
  });

  CKEDITOR.config.imagePath = '$imagePath';
  CKEDITOR.config.imageUrl = '$imageUrl';
  CKEDITOR.config.imageBrowserUploadUrl = '$this->imageBrowserUploadUrl';
  CKEDITOR.config.currentInstance = CKEDITOR.instances.$editorName;
</script>
HEREDOC;

    if ($this->metaNamesJs) {
      $metaNamesJs = $this->metaNamesJs;
      $str .= <<<HEREDOC
<script type="text/javascript">
CKEDITOR.config.metaNamesJs = $metaNamesJs;
</script>
HEREDOC;
    }

    return($str);
  }

}

?>
