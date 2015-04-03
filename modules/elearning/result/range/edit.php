<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningResultRangeId = LibEnv::getEnvHttpPOST("elearningResultRangeId");
  $upperRange = LibEnv::getEnvHttpPOST("upperRange");
  $grade = LibEnv::getEnvHttpPOST("grade");

  $upperRange = LibString::cleanString($upperRange);
  $grade = LibString::cleanString($grade);

  // The upper value for the range is required
  if (!$upperRange) {
    array_push($warnings, $mlText[6]);
  }

  // The grade for the range is required
  if (!$grade) {
    array_push($warnings, $mlText[3]);
  }

  // The upper value for the range must be between 1 and 100
  if ($upperRange < 1 || $upperRange > 100) {
    array_push($warnings, $mlText[12]);
  }

  if (count($warnings) == 0) {

    if ($resultRange = $elearningResultRangeUtils->selectById($elearningResultRangeId)) {
      $resultRange->setUpperRange($upperRange);
      $resultRange->setGrade($grade);
      $elearningResultRangeUtils->update($resultRange);
    } else {
      $resultRange = new ElearningResultRange();
      $resultRange->setUpperRange($upperRange);
      $resultRange->setGrade($grade);
      $elearningResultRangeUtils->insert($resultRange);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/result/range/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningResultRangeId = LibEnv::getEnvHttpGET("elearningResultRangeId");

  $upperRange = '';
  $grade = '';
  if ($elearningResultRange = $elearningResultRangeUtils->selectById($elearningResultRangeId)) {
    $upperRange = $elearningResultRange->getUpperRange();
    $grade = $elearningResultRange->getGrade();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/result/range/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[1], $mlText[4], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='grade' value='$grade' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[14], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='upperRange' value='$upperRange' size='3' maxlength='3'>");
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningResultRangeId', $elearningResultRangeId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
