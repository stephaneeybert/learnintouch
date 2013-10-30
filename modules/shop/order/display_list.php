<?PHP

require_once("website.php");

$userUtils->checkUserLogin();

$userId = $userUtils->getLoggedUserId();

$str = $shopOrderUtils->renderList($userId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
