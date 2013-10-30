<?PHP

require_once("website.php");

$shopItemId = LibEnv::getEnvHttpGET("shopItemId");
$shopItemImageId = LibEnv::getEnvHttpGET("shopItemImageId");

// Prevent sql injection attacks as the id is always numeric
$shopItemId = (int) $shopItemId;
$shopItemImageId = (int) $shopItemImageId;

$str = $shopItemUtils->renderItem($shopItemId, $shopItemImageId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
