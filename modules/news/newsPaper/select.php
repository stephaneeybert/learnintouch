<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");

  if ($newsPaperId) {
    $str = $templateUtils->renderJsUpdate($newsPaperId);
    printMessage($str);

    $str = LibJavascript::autoCloseWindow();
    printContent($str);
    return;
  } else {
    array_push($warnings, $mlText[4]);
  }

} else {

  $newsPaperId = '';

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/select.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$strJsSuggest = $commonUtils->ajaxAutocomplete("$gNewsUrl/newsPaper/suggestNewsPaperInternalLinks.php", "newsPaperName", "newsPaperId");
$panelUtils->addContent($strJsSuggest);
$panelUtils->addHiddenField('newsPaperId', $newsPaperId);
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' id='newsPaperName' value='' size='40' />");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
