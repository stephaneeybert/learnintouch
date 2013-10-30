<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningLevelId = LibEnv::getEnvHttpPOST("elearningLevelId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
    }

  if (count($warnings) == 0) {

  if ($level = $elearningLevelUtils->selectById($elearningLevelId)) {
    $level->setName($name);
    $level->setDescription($description);
    $elearningLevelUtils->update($level);
    } else {
    $level = new ElearningLevel();
    $level->setName($name);
    $level->setDescription($description);
    $elearningLevelUtils->insert($level);
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/level/admin.php");
  printContent($str);
  return;

  }

  } else {

  $elearningLevelId = LibEnv::getEnvHttpGET("elearningLevelId");

  $name = '';
  $description = '';
  if ($elearningLevelId) {
    if ($level = $elearningLevelUtils->selectById($elearningLevelId)) {
      $name = $level->getName();
      $description = $level->getDescription();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/level/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningLevelId', $elearningLevelId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
