<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningCategoryId = LibEnv::getEnvHttpPOST("elearningCategoryId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
    }

  if (count($warnings) == 0) {

  if ($elearningCategory = $elearningCategoryUtils->selectById($elearningCategoryId)) {
    $elearningCategory->setName($name);
    $elearningCategory->setDescription($description);
    $elearningCategoryUtils->update($elearningCategory);
    } else {
    $elearningCategory = new ElearningCategory();
    $elearningCategory->setName($name);
    $elearningCategory->setDescription($description);
    $elearningCategoryUtils->insert($elearningCategory);
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/category/admin.php");
  printContent($str);
  return;

  }

  } else {

  $elearningCategoryId = LibEnv::getEnvHttpGET("elearningCategoryId");

  $name = '';
  $description = '';
  if ($elearningCategoryId) {
    if ($elearningCategory = $elearningCategoryUtils->selectById($elearningCategoryId)) {
      $name = $elearningCategory->getName();
      $description = $elearningCategory->getDescription();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/category/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningCategoryId', $elearningCategoryId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
