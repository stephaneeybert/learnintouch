<?PHP

require_once("website.php");
require_once($gDynpagePath . "globals.php");
require_once($gDynpagePath . "Dynpage.php");
require_once($gDynpagePath . "DynpageDao.php");
require_once($gDynpagePath . "DynpageDB.php");
require_once($gDynpagePath . "DynpageUtils.php");

if (isset($pageId)) {
  $dynpageId = $pageId;
} else {
  $dynpageId = LibEnv::getEnvHttpGET("dynpageId");
}

// Prevent sql injection attacks as the id is always numeric
$dynpageId = (int) $dynpageId;


LibSession::putSessionValue(DYNPAGE_SESSION_USER_PAGE, $dynpageId);

if ($dynpage = $dynpageUtils->selectById($dynpageId)) {
  $gTemplate->setPageContent($dynpageUtils->render($dynpage));

  $preferenceUtils->init($dynpageUtils->preferences);
  if ($preferenceUtils->getValue("DYNPAGE_NAME_AS_TITLE")) {
    $name = $dynpage->getName();
    if ($name) {
      $gTemplate->setPageTitle($name);
    }
  }

  // Do not activate the webpage template model if no webpage is specified
  // This can be the case if no entry page is specified for the website
  // If the website entry model is different this would lead to the webpage model being used when it should not
  $dynpageTemplateModelId = $dynpageUtils->getTemplateModel();  
  if ($dynpageTemplateModelId > 0) {
    $templateModelId = $dynpageTemplateModelId;
  }
}

require_once($gTemplatePath . "render.php");

// Reset the current displayed page
// This is to avoid keeping a current displayed page when leaving for a system page
LibSession::putSessionValue(DYNPAGE_SESSION_USER_PAGE, '');

?>
