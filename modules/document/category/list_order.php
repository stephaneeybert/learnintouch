<?PHP

require_once("website.php");

LibHtml::preventCaching();


$documentCategoryIds = LibEnv::getEnvHttpPOST("documentCategoryIds");

$listOrder = 1;
foreach ($documentCategoryIds as $documentCategoryId) {
  if ($documentCategory = $documentCategoryUtils->selectById($documentCategoryId)) {
    $documentCategory->setListOrder($listOrder);
    $documentCategoryUtils->update($documentCategory);
    $listOrder++;
  }
}

?>
