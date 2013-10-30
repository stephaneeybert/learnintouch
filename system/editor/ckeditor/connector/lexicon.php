<?php

require_once("website.php");

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $lexiconEntryId = LibEnv::getEnvHttpPOST("lexiconEntryId");

  $tooltipDomId = $lexiconEntryUtils->renderDomId($lexiconEntryId);
  $tooltipClassName = LEXICON_ENTRY_CLASS_NAME;

  $str = <<<HEREDOC
<script type='text/javascript'>
  var currentEditor = window.opener.CKEDITOR.config.currentInstance;
  if (currentEditor != undefined) {
    var selectedContent = currentEditor._.selectedContent;
    if (selectedContent != undefined) {
      currentEditor.insertHtml('<span class="$tooltipClassName" lexicon_entry_id="$lexiconEntryId" id="$tooltipDomId">' + selectedContent + '</span>');
    }
  }
</script>
HEREDOC;

  $str .= LibJavascript::autoCloseWindow();
  printContent($str);
  return;

}

require_once($gLexiconPath . "select.php");

?>
