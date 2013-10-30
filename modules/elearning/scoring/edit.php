<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningScoringId = LibEnv::getEnvHttpPOST("elearningScoringId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $requiredScore = LibEnv::getEnvHttpPOST("requiredScore");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $requiredScore = LibString::cleanString($requiredScore);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($scoring = $elearningScoringUtils->selectById($elearningScoringId)) {
      $scoring->setName($name);
      $scoring->setDescription($description);
      $scoring->setRequiredScore($requiredScore);
      $elearningScoringUtils->update($scoring);
    } else {
      $scoring = new ElearningScoring();
      $scoring->setName($name);
      $scoring->setDescription($description);
      $scoring->setRequiredScore($requiredScore);
      $elearningScoringUtils->insert($scoring);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/scoring/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningScoringId = LibEnv::getEnvHttpGET("elearningScoringId");

  $name = '';
  $description = '';
  $requiredScore = '';
  if ($scoring = $elearningScoringUtils->selectById($elearningScoringId)) {
    $name = $scoring->getName();
    $description = $scoring->getDescription();
    $requiredScore = $scoring->getRequiredScore();
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/scoring/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='requiredScore' value='$requiredScore' size='3' maxlength='3' style='text-align:right;'> %");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningScoringId', $elearningScoringId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
