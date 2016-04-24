<?PHP

require_once("website.php");

LibHtml::preventCaching();


$documentIds = LibEnv::getEnvHttpPOST("documentIds");

$listOrder = 1;
foreach ($documentIds as $documentId) {
  if ($document = $documentUtils->selectById($documentId)) {
    $document->setListOrder($listOrder);
    $documentUtils->update($document);
    $listOrder++;
  }
}

?>
