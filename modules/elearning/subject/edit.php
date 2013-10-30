<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningSubjectId = LibEnv::getEnvHttpPOST("elearningSubjectId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
    }

  if (count($warnings) == 0) {

  if ($subject = $elearningSubjectUtils->selectById($elearningSubjectId)) {
    $subject->setName($name);
    $subject->setDescription($description);
    $elearningSubjectUtils->update($subject);
    } else {
    $subject = new ElearningSubject();
    $subject->setName($name);
    $subject->setDescription($description);
    $elearningSubjectUtils->insert($subject);
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/subject/admin.php");
  printContent($str);
  return;

  }

  } else {

  $elearningSubjectId = LibEnv::getEnvHttpGET("elearningSubjectId");

  $name = '';
  $description = '';
  if ($elearningSubjectId) {
    if ($subject = $elearningSubjectUtils->selectById($elearningSubjectId)) {
      $name = $subject->getName();
      $description = $subject->getDescription();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/subject/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningSubjectId', $elearningSubjectId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
