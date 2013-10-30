<?PHP

// Set a custom error handler
$previousErrorHandler = set_error_handler("errorHandler");
// Disable the standard error reporting as errors are handled by an error handling function
if (!isDebug()) {
  error_reporting(0);
}

$gTemplate = new Template();
$gIsPhoneClient = $templateUtils->isPhoneClient();
$gIsTouchClient = $templateUtils->isTouchClient();

$gWebsiteTitle = $profileUtils->getWebsiteTitle();
if (LibUtils::isCLI()) {
  $gAdminSessionLogin = $adminUtils->getSessionLogin();
}
$gCurrentLanguageCode = $languageUtils->getCurrentLanguageCode();
$gCurrentAdminLanguageCode = $languageUtils->getCurrentAdminLanguageCode();

require_once($gAdminPath . "module/modules.php");
require_once($gAdminPath . "module/moduleNames.php");
require_once($gFormPath . "item/types.php");
require_once($gFormPath . "valid/types.php");
require_once($gElearningPath . "question/questionTypes.php");
require_once($gElearningPath . "question/hintPlacements.php");
require_once($gImageSetPath . "init.php");

?>
