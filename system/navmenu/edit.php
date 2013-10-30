<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navmenuId = LibEnv::getEnvHttpPOST("navmenuId");
  $hide = LibEnv::getEnvHttpPOST("hide");

  $hide = LibString::cleanString($hide);

  if (count($warnings) == 0) {

    if ($navmenu = $navmenuUtils->selectById($navmenuId)) {
      $navmenu->setHide($hide);
      $navmenuUtils->update($navmenu);
    }

    $str = LibHtml::urlRedirect("$gNavmenuUrl/admin.php?navmenuId=$navmenuId");
    printContent($str);
    return;
  }

}

$navmenuId = LibEnv::getEnvHttpGET("navmenuId");
if (!$navmenuId) {
  $navmenuId = LibEnv::getEnvHttpPOST("navmenuId");
}

if (!$formSubmitted) {
  $hide = '';
  if ($navmenuId) {
    if ($navmenu = $navmenuUtils->selectById($navmenuId)) {
      $hide = $navmenu->getHide();
    }
  }
}

if ($hide == '1') {
  $checkedHide = "CHECKED";
} else {
  $checkedHide = '';
}

$panelUtils->setHeader($mlText[0], "$gNavmenuUrl/admin.php?navmenuId=$navmenuId");

if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $panelUtils->addLine($panelUtils->addCell($warning, "w"));
  }
}

$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[3], $mlText[8], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hide' $checkedHide value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('navmenuId', $navmenuId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
