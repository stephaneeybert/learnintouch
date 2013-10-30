<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLessonHeadingId = LibEnv::getEnvHttpPOST("elearningLessonHeadingId");
  $name = LibEnv::getEnvHttpPOST("name");
  $content = LibEnv::getEnvHttpPOST("content");

  $name = LibString::cleanString($name);
  $content = LibString::cleanString($content);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  if (count($warnings) == 0) {

    if ($elearningLessonHeading = $elearningLessonHeadingUtils->selectById($elearningLessonHeadingId)) {
      $elearningLessonHeading->setName($name);
      $elearningLessonHeading->setContent($content);
      $elearningLessonHeadingUtils->update($elearningLessonHeading);
    } else {
      $elearningLessonHeading = new ElearningCategory();
      $elearningLessonHeading->setName($name);
      $elearningLessonHeading->setContent($content);
      $elearningLessonHeadingUtils->insert($elearningLessonHeading);
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/lesson/heading/admin.php");
    printContent($str);
    return;

  }

} else {

  $elearningLessonHeadingId = LibEnv::getEnvHttpGET("elearningLessonHeadingId");

  $name = '';
  $content = '';
  if ($elearningLessonHeadingId) {
    if ($elearningLessonHeading = $elearningLessonHeadingUtils->selectById($elearningLessonHeadingId)) {
      $name = $elearningLessonHeading->getName();
      $content = $elearningLessonHeading->getContent();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/lesson/heading/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='content' value='$content' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningLessonHeadingId', $elearningLessonHeadingId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
