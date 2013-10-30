<?PHP

require_once("website.php");

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $lexiconEntryId = LibEnv::getEnvHttpPOST("lexiconEntryId");

  $tooltipDomId = $lexiconEntryUtils->renderDomId($lexiconEntryId);
  $tooltipClassName = LEXICON_ENTRY_CLASS_NAME;

  $str = <<<HEREDOC
<script language="javascript" type="text/javascript">

function getRange() {
  var userSelection = getUserSelection();
  if (navigator.appName.indexOf('Microsoft') != -1) {
    if (userSelection.rangeCount > 0) {
      return userSelection.getRangeAt(0);
    }
  }
  if (userSelection.getRangeAt) {
    if (userSelection.rangeCount > 0) {
      return userSelection.getRangeAt(0);
    }
  }
}

function getUserSelection() {
  var oName = window.opener.oUtil.oName;
  var oEditor = window.opener.document.getElementById("idContent"+oName);
  var oContentArea = oEditor.contentWindow;
  if (navigator.appName.indexOf('Microsoft') != -1) {
    var doc = oContentArea.document;
    var oSel = doc.selection.createRange();
    var selectionType = doc.selection.type;
    if (selectionType == "Control" || selectionType == "None") {
      return;
    }
  } else {
    var oSel = oContentArea.getSelection();
  }
  return(oSel);
}

function insertLexiconEntry(lexiconEntryId) {
  var span = document.createElement('span');
  span.className = "$tooltipClassName";
  span.id = "$tooltipDomId";
  span.lexicon_entry_id = "$lexiconEntryId";
  var oSel = getUserSelection();
  if (navigator.appName.indexOf('Microsoft') != -1) {
    var sHTML = oSel.htmlText;
    span.innerHTML = sHTML;
    oSel.pasteHTML(span.outerHTML);
  } else {
    var range = getRange();
    range.surroundContents(span);
  }
}

</script>

HEREDOC;

  if ($lexiconEntry = $lexiconEntryUtils->selectById($lexiconEntryId)) {
    $str .= <<<HEREDOC
<script type="text/javascript" src="$gJsUrl/jquery/jquery-1.5.1.min.js"></script>
<script language="javascript" type="text/javascript">
$(document).ready(function() {
  insertLexiconEntry('$lexiconEntryId');
});
</script>
HEREDOC;
  }

  $str .= LibJavascript::autoCloseWindow();
  printContent($str);
  return;

}

require_once($gLexiconPath . "select.php");

?>
