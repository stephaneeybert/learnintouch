<?PHP

require_once("website.php");

LibHtml::preventCaching();

$linkCategoryIds = LibEnv::getEnvHttpPOST("linkCategoryIds");

$listOrder = 1;
foreach ($linkCategoryIds as $linkCategoryId) {
  if ($linkCategory = $linkCategoryUtils->selectById($linkCategoryId)) {
    $linkCategory->setListOrder($listOrder);
    $linkCategoryUtils->update($linkCategory);
    $listOrder++;
  }
}

?>
