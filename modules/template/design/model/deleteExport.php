<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $name = LibEnv::getEnvHttpPOST("name");

  // Delete the file
  LibFile::deleteFile($gTemplateDataPath . "export/xml/" . $name);

  $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/listExport.php");
  printContent($str);
  return;

  } else {

  $name = LibEnv::getEnvHttpGET("name");

  $panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/listExport.php");

  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('name', $name);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
