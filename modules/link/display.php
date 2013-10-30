<?PHP

require_once("website.php");

$linkCategoryId = LibEnv::getEnvHttpGET("linkCategoryId");
if (!$linkCategoryId) {
  $linkCategoryId = LibEnv::getEnvHttpPOST("linkCategoryId");
}

// Prevent sql injection attacks as the id is always numeric
$linkCategoryId = (int) $linkCategoryId;

$preferenceUtils->init($linkUtils->preferences);
$displayAll = $preferenceUtils->getValue("LINK_DISPLAY_ALL");

if (!$displayAll) {
  if (!$linkCategoryId) {
    $linkCategoryId = LibSession::getSessionValue(NAVLINK_SESSION_CATEGORY);
  } else {
    LibSession::putSessionValue(NAVLINK_SESSION_CATEGORY, $linkCategoryId);
  }
}

$str = $linkCategoryUtils->render($linkCategoryId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
