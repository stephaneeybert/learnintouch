<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_TEMPLATE);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $navbarId = LibEnv::getEnvHttpPOST("navbarId");
  $hide = LibEnv::getEnvHttpPOST("hide");

  $hide = LibString::cleanString($hide);

  if (count($warnings) == 0) {

    if ($navbar = $navbarUtils->selectById($navbarId)) {
      $navbar->setHide($hide);
      $navbarUtils->update($navbar);
      }

    $str = LibHtml::urlRedirect("$gNavbarUrl/admin.php?navbarId=$navbarId");
    printContent($str);
    return;
    }

  }

$navbarId = LibEnv::getEnvHttpGET("navbarId");
if (!$navbarId) {
  $navbarId = LibEnv::getEnvHttpPOST("navbarId");
  }

if (!$formSubmitted) {
  $hide = '';
  if ($navbarId) {
    if ($navbar = $navbarUtils->selectById($navbarId)) {
      $hide = $navbar->getHide();
      }
    }
  }

if ($hide == '1') {
  $checkedHide = "CHECKED";
  } else {
  $checkedHide = '';
  }

$panelUtils->setHeader($mlText[0], "$gNavbarUrl/admin.php?navbarId=$navbarId");

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
$panelUtils->addHiddenField('navbarId', $navbarId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
