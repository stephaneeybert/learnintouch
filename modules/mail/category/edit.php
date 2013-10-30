<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_MAIL);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $categoryId = LibEnv::getEnvHttpPOST("categoryId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
    }

  if (count($warnings) == 0) {

  if ($mailCategory = $mailCategoryUtils->selectById($categoryId)) {
    $mailCategory->setName($name);
    $mailCategory->setDescription($description);
    $mailCategoryUtils->update($mailCategory);
    } else {
    $mailCategory = new MailCategory();
    $mailCategory->setName($name);
    $mailCategory->setDescription($description);
    $mailCategoryUtils->insert($mailCategory);
    }

  $str = LibHtml::urlRedirect("$gMailUrl/category/admin.php");
  printContent($str);
  return;

  }

  } else {

  $categoryId = LibEnv::getEnvHttpGET("categoryId");

  $name = '';
  $description = '';
  if ($categoryId) {
    if ($mailCategory = $mailCategoryUtils->selectById($categoryId)) {
      $name = $mailCategory->getName();
      $description = $mailCategory->getDescription();
      }
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gMailUrl/category/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('categoryId', $categoryId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
