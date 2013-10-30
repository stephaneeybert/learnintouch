<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$elementId = LibEnv::getEnvHttpGET("elementId");

$warnings = array();

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

// Allow a redirection to the select popup window of the appropriate editor
LibSession::putSessionValue(LEXICON_SESSION_SELECT_URL, $REQUEST_URI);

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gLexiconUrl/suggest.php", "lexiconEntryName", "lexiconEntryId", 100);
$panelUtils->addContent($strJsSuggest);
$strCommand = "<a id='addEntryUrl' href='#' $gJSNoStatus>"
  . "<img id='addEntryImg' border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[3]'></a>"
  . " <a id='editEntryUrl' href='$gLexiconUrl/edit.php' $gJSNoStatus>"
  . "<img id='editEntryImg' style='display:none;' border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[5]'></a>"
  . " <a id='deleteEntryUrl' href='' $gJSNoStatus>"
  . "<img border='0' id='deleteEntryImg' style='display:none;' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[4]'></a>"
  . " <a href='$gLexiconUrl/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[8]'></a>";
$panelUtils->openForm("$PHP_SELF");
$label = $popupUtils->getTipPopup($mlText[2], $mlText[7], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell("<input type='text' id='lexiconEntryName' value='' size='40' /> $strCommand", "n"));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elementId', $elementId);
$panelUtils->addLine("", "<input type='text' id='lexiconEntryId' name='lexiconEntryId' style='display:none;' value='' />");
$panelUtils->closeForm();
$strJsUpdate = <<<HEREDOC
<script language="javascript" type="text/javascript">
$(document).ready(function() {
  $("#addEntryUrl").click(function() {
    var name = $("#lexiconEntryName").val();
    window.location = "$gLexiconUrl/edit.php?name=" + name;
    });
  $("#lexiconEntryId").change(function() {
    if (this.value) {
      $("#addEntryImg").attr("style", "display:none;");
      $("#editEntryUrl").attr("href", "$gLexiconUrl/edit.php?lexiconEntryId=" + this.value);
      $("#editEntryImg").attr("style", "display:inline;");
      $("#deleteEntryUrl").attr("href", "$gLexiconUrl/delete.php?lexiconEntryId=" + this.value);
      $("#deleteEntryImg").attr("style", "display:inline;");
    }
  });
});
</script>
HEREDOC;
$panelUtils->addContent($strJsUpdate);
$panelUtils->addLine();
$str = $panelUtils->render();

printAdminPage($str);

?>
