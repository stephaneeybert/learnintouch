<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $lexiconEntryId = LibEnv::getEnvHttpPOST("lexiconEntryId");

  if (count($warnings) == 0) {

    $lexiconEntryUtils->delete($lexiconEntryId);

    $str = LibHtml::urlRedirect("$gLexiconUrl/admin.php");
    printContent($str);
    exit;

  }

} else {

  $lexiconEntryId = LibEnv::getEnvHttpGET("lexiconEntryId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $name = '';
  $explanation = '';
  if ($lexiconEntryId) {
    if ($lexiconEntry = $lexiconEntryUtils->selectById($lexiconEntryId)) {
      $name = $lexiconEntry->getName();
      $explanation = $lexiconEntry->getExplanation();
    }
  }

}

$panelUtils->setHeader($mlText[0], "$gLexiconUrl/admin.php");
$panelUtils->openForm($PHP_SELF, "edit");
$strCommand = "<a href='$gLexiconUrl/admin.php' $gJSNoStatus>"
  . "<img border='0' src='$gCommonImagesUrl/$gImageList' title='$mlText[4]'></a>";
$panelUtils->addLine($panelUtils->addCell($strCommand, "nbr"));
$label = $popupUtils->getTipPopup($mlText[1], $mlText[3], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $explanation);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $panelUtils->addCell($mlText[3], ""));
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('lexiconEntryId', $lexiconEntryId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
