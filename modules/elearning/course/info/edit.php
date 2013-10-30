<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCourseInfoId = LibEnv::getEnvHttpPOST("elearningCourseInfoId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");
  $headline = LibEnv::getEnvHttpPOST("headline");
  $information = LibEnv::getEnvHttpPOST("information");

  $headline = LibString::cleanString($headline);
  $information = LibString::cleanHtmlString($information);

  // The headline is required
  if (!$headline) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    if ($elearningCourseInfo = $elearningCourseInfoUtils->selectById($elearningCourseInfoId)) {
      $elearningCourseInfo->setHeadline($headline);
      $elearningCourseInfo->setInformation($information);
      $elearningCourseInfoUtils->update($elearningCourseInfo);
    } else {
      $elearningCourseInfo = new ElearningCourseInfo();
      $elearningCourseInfo->setHeadline($headline);
      $elearningCourseInfo->setInformation($information);
      $listOrder = $elearningCourseInfoUtils->getNextListOrder($elearningCourseId);
      $elearningCourseInfo->setListOrder($listOrder);
      $elearningCourseInfo->setElearningCourseId($elearningCourseId);
      $elearningCourseInfoUtils->insert($elearningCourseInfo);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/course/info/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningCourseInfoId = LibEnv::getEnvHttpGET("elearningCourseInfoId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  $headline = '';
  $information = '';
  if ($elearningCourseInfoId) {
    if ($elearningCourseInfo = $elearningCourseInfoUtils->selectById($elearningCourseInfoId)) {
      $headline = $elearningCourseInfo->getHeadline();
      $information = $elearningCourseInfo->getInformation();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/course/info/admin.php?elearningCourseInfoId=$elearningCourseInfoId");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nb"));
$panelUtils->addLine("<input type='text' name='headline' value='$headline' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[23], 300, 200);
if ($elearningExerciseUtils->useHtmlEditorInnova()) {
  $oInnovaContentName = "information";
  include($gInnovaHtmlEditorPath . "setupElearningCourseInfo.php");
  $panelUtils->addContent($gInnovaHead);
  $strEditor = "<textarea id='$oInnovaContentName' name='$oInnovaContentName'>$information</textarea> $gInnovaBodyOpen $gInnovaBodyClose";
} else {
  include($gHtmlEditorPath . "CKEditorUtils.php");
  $contentEditor = new CKEditorUtils();
  $contentEditor->languageUtils = $languageUtils;
  $contentEditor->commonUtils = $commonUtils;
  $contentEditor->load();
  $contentEditor->withReducedToolbar();
  $strEditor = $contentEditor->render();
  $strEditor .= $contentEditor->renderInstance("information", $information);
}
$panelUtils->addLine($panelUtils->addCell($label, "nb"));
$panelUtils->addLine($strEditor);
$panelUtils->addHiddenField('elearningCourseInfoId', $elearningCourseInfoId);
$panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
