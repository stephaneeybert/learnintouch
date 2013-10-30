<?PHP

require_once("website.php");
require_once($gDynpagePath . "globals.php");
require_once($gDynpagePath . "Dynpage.php");
require_once($gDynpagePath . "DynpageDao.php");
require_once($gDynpagePath . "DynpageDB.php");
require_once($gDynpagePath . "DynpageUtils.php");


$languageCode = $languageUtils->getCurrentLanguageCode();
if ($gIsPhoneClient) {
  $entryPageId = $templateUtils->getPhoneEntryPage($languageCode);
  } else {
  $entryPageId = $templateUtils->getComputerEntryPage($languageCode);
  }

if ($dynpage = $dynpageUtils->selectById($entryPageId)) {
  $gTemplate->setPageContent($dynpageUtils->render($dynpage));

  $preferenceUtils->init($dynpageUtils->preferences);
  if ($preferenceUtils->getValue("DYNPAGE_NAME_AS_TITLE")) {
    $name = $dynpage->getName();
    if ($name) {
      $gTemplate->setPageTitle($name);
      }
    }
  }

$dynpageTemplateModelId = $dynpageUtils->getTemplateModel();
if ($dynpageTemplateModelId > 0) {
  $templateModelId = $dynpageTemplateModelId;
}

require_once($gTemplatePath . "render.php");

// Reset the current displayed page
// This is to avoid keeping a current displayed page when leaving for a system page
LibSession::putSessionValue(DYNPAGE_SESSION_USER_PAGE, '');

?>
