<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_FORM);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $formId = LibEnv::getEnvHttpPOST("formId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
    }

  // Check that the name is not already used
  if ($form = $formUtils->selectByName($name)) {
    $wFormId = $form->getId();
    if ($wFormId != $formId) {
      array_push($warnings, $mlText[7]);
      }
    }

  if (count($warnings) == 0) {

  // Duplicate the form
  $formUtils->duplicate($formId, $name);

  $str = LibHtml::urlRedirect("$gFormUrl/admin.php");
  printMessage($str);
  exit;

  }

  } else {

  $formId = LibEnv::getEnvHttpGET("formId");

  $name = '';
  $description = '';
  if ($form = $formUtils->selectById($formId)) {
    $randomNumber = LibUtils::generateUniqueId();
    $name = $form->getName() . FORM_DUPLICATA . '_' . $randomNumber;
    $description = $form->getDescription();
    }

  }

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gFormUrl/admin.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $help = $popupUtils->getHelpPopup($mlText[3], 300, 300);
  $panelUtils->setHelp($help);
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
  $panelUtils->addLine();
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
  $panelUtils->addLine();
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('formId', $formId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
