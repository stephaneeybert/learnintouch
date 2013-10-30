<?php

require_once("website.php");

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $lexiconEntryId = LibEnv::getEnvHttpPOST("lexiconEntryId");
  $elementId = LibEnv::getEnvHttpPOST("elementId");

  $tooltipDomId = $lexiconEntryUtils->renderDomId($lexiconEntryId);
  $tooltipClassName = LEXICON_ENTRY_CLASS_NAME;

  $str = <<<HEREDOC
<script type='text/javascript'>
  var elementId = '$elementId';
  var parentDocument = window.opener.document;
  var element = parentDocument.getElementById(elementId);
  var selectedText = getUserSelectedText(parentDocument, element);
  if (isNotUndefined('selectedText')) {
    var fullText = parentDocument.getElementById(elementId).value;
    var split = fullText.split(selectedText);
    if (split.length > 0) {
      var prefix = split[0];
      var suffix = '';
      if (split.length > 1) {
        suffix = split[1];
      }
      var replacement = '<span class="$tooltipClassName" lexicon_entry_id="$lexiconEntryId" id="$tooltipDomId">' + selectedText + '</span>';
      parentDocument.getElementById(elementId).value = prefix + replacement + suffix;
    }
  }
</script>
HEREDOC;

  $str .= LibJavascript::autoCloseWindow();
  printAdminPage($str);
  return;

}

require_once($gLexiconPath . "select.php");

?>
