<?PHP

require_once("website.php");

$itemType = LibEnv::getEnvHttpGET("itemType");
$itemId = LibEnv::getEnvHttpGET("itemId");
$delete = LibEnv::getEnvHttpGET("delete");


if ($itemType && $itemId) {
  if ($delete) {
    $shopItemUtils->deleteFromSelection($itemType, $itemId);
    } else {
    $shopItemUtils->addToSelection($itemType, $itemId);
    }

  // A redirection is needed to allow the cookie to be written before being read again
  $str = LibHtml::urlRedirect($PHP_SELF);
  printContent($str);
  return;
  }

$str = $shopItemUtils->renderSelection();

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
