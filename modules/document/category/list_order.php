<?PHP

require_once("website.php");

LibHtml::preventCaching();


$documentCategoryIds = LibEnv::getEnvHttpPOST("documentCategoryIds");

$listOrder = 1;
foreach ($documentCategoryIds as $documentCategoryId) {
  // An ajax request parameter value is UTF-8 encoded
  $documentCategoryId = utf8_decode($documentCategoryId);

  if ($documentCategory = $documentCategoryUtils->selectById($documentCategoryId)) {
    $documentCategory->setListOrder($listOrder);
    $documentCategoryUtils->update($documentCategory);
    $listOrder++;
  }
}

?>
