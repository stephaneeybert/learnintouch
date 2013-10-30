<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningMatterId = LibEnv::getEnvHttpPOST("elearningMatterId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
    }

  if (count($warnings) == 0) {

  if ($matter = $elearningMatterUtils->selectById($elearningMatterId)) {
    $matter->setName($name);
    $matter->setDescription($description);
    $elearningMatterUtils->update($matter);
    } else {
    $matter = new ElearningMatter();
    $matter->setName($name);
    $matter->setDescription($description);
    $elearningMatterUtils->insert($matter);
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/matter/admin.php");
  printContent($str);
  return;

  }

  } else {

  $elearningMatterId = LibEnv::getEnvHttpGET("elearningMatterId");

  $name = '';
  $description = '';
  if ($elearningMatterId) {
    if ($matter = $elearningMatterUtils->selectById($elearningMatterId)) {
      $name = $matter->getName();
      $description = $matter->getDescription();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/matter/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningMatterId', $elearningMatterId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
