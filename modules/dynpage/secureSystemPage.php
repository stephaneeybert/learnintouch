<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_DYNPAGE);

$mlText = $languageUtils->getMlText(__FILE__);


$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $pageId = LibEnv::getEnvHttpPOST("pageId");
  $secure = LibEnv::getEnvHttpPOST("secure");

  $secure = LibString::cleanString($secure);

  if ($secure) {
    $userUtils->securePage($pageId);
    } else {
    $userUtils->unsecurePage($pageId);
    }

  $str = LibHtml::urlRedirect("$gDynpageUrl/secureAdmin.php");
  printContent($str);
  return;

  } else {

  $pageId = LibEnv::getEnvHttpGET("pageId");

  $name = $templateUtils->getSystemPageName($pageId);

  $secure = LibEnv::getEnvHttpGET("secure");

  if ($secure == 1) {
    $mlTextSecure = $mlText[2];
    } else {
    $mlTextSecure = $mlText[5];
    }

  $panelUtils->setHeader($mlText[0], "$gDynpageUrl/secureAdmin.php");

  if ($secure == 1) {
    $str = $dynpageUtils->getSecureWarning();
    if ($str) {
      $panelUtils->addLine('', $str);
      $panelUtils->addLine();
      }
    }

  $panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), $name);
  $panelUtils->addLine();
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlTextSecure, "br"), $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('pageId', $pageId);
  $panelUtils->addHiddenField('secure', $secure);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);
  }

?>
