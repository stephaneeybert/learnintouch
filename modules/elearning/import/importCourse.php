<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $contentImportId = LibEnv::getEnvHttpPOST("contentImportId");
  $elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");
  $elearningCourseId = LibEnv::getEnvHttpPOST("elearningCourseId");

  // The matter is required
  if (!$elearningMatterId) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    $xmlResponse = $elearningImportUtils->exposeCourseAsXML($contentImportId, $elearningCourseId, true);
    $lastInsertElearningCourseId = $elearningImportUtils->importCourseREST($contentImportId, $xmlResponse, $elearningMatterId);

    $panelUtils->setHeader($mlText[0], "$gElearningUrl/import/admin.php");
    $panelUtils->openForm($PHP_SELF);
    if ($lastInsertElearningCourseId) {
      if ($elearningCourse = $elearningCourseUtils->selectById($lastInsertElearningCourseId)) {
        $name = $elearningCourse->getName();
        $description = $elearningCourse->getDescription();
        $panelUtils->addLine($panelUtils->addCell($mlText[10], "ng"));
        $panelUtils->addLine();
        $strName = "<a href='$gElearningUrl/exercise/admin.php?currentElearningCourseId=$lastInsertElearningCourseId' title='$mlText[14]' $gJSNoStatus>" . $name . "</a>";
        $panelUtils->addLine($panelUtils->addCell($mlText[13], "nbr"), $strName);
      }
    } else {
      $panelUtils->addLine($panelUtils->addCell($mlText[11], "nw"));
    }
    $panelUtils->addLine();
    $panelUtils->addLine('', $panelUtils->getOk());
    $panelUtils->addHiddenField('formSubmitted', 2);
    $panelUtils->closeForm();
    $str = $panelUtils->render();
    printAdminPage($str);

  } else if ($formSubmitted == 2) {

    $str = LibHtml::urlRedirect("$gElearningUrl/import/admin.php");
    printContent($str);
    return;

  }

} else {

  $contentImportId = LibEnv::getEnvHttpGET("contentImportId");
  $elearningCourseId = LibEnv::getEnvHttpGET("elearningCourseId");

  // Get the course details and content
  $xmlResponse = $elearningImportUtils->exposeCourseAsXML($contentImportId, $elearningCourseId, false);
  $courseContent = $elearningImportUtils->getCourseContentREST($contentImportId, $xmlResponse);
  list($unusedEearningCourseId, $name, $description, $image, $courseItems) = $courseContent;

  // The name of the course must be retrieved
  if (!$name) {
    array_push($warnings, $mlText[8]);
  }

  $elearningMatters = $elearningMatterUtils->selectAll();
  $elearningMatterList = Array('' => '');
  foreach ($elearningMatters as $elearningMatter) {
    $wMatterId = $elearningMatter->getId();
    $wName = $elearningMatter->getName();
    $elearningMatterList[$wMatterId] = $wName;
  }
  $strSelectMatter = LibHtml::getSelectList("elearningMatterId", $elearningMatterList);

  $strWarning = '';
  if (count($warnings) > 0) {
    foreach ($warnings as $warning) {
      $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/import/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $description);
  $panelUtils->addLine();
  if (count($courseItems) > 0) {
    foreach ($courseItems as $courseItem) {
      list($type, $id, $name, $description, $image) = $courseItem;
      if ($description) {
        $name .= ' - ' . $description;
      }
      if ($type == ELEARNING_XML_EXERCISE) {
        $panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), $name);
      } else if ($type == ELEARNING_XML_LESSON) {
        $panelUtils->addLine($panelUtils->addCell($mlText[9], "nbr"), $name);
      }
    }
    $panelUtils->addLine();
  }
  $label = $popupUtils->getTipPopup($mlText[5], $mlText[6], 300, 300);
  $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectMatter);
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('contentImportId', $contentImportId);
  $panelUtils->addHiddenField('elearningCourseId', $elearningCourseId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
}

?>
