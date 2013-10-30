<?PHP

require_once("website.php");
require_once($gPearPath . "Tree.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $name = LibEnv::getEnvHttpPOST("name");

  if (count($warnings) == 0) {

    $templateModelUtils->importXML($name);

    $str = LibHtml::urlRedirect("$gTemplateUrl/design/model/admin.php");
    printContent($str);
    return;

  }

} else {

  $name = LibEnv::getEnvHttpGET("name");

}

$panelUtils->setHeader($mlText[0], "$gTemplateUrl/design/model/admin.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $name);
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('name', $name);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
