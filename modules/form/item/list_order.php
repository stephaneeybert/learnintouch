<?PHP

require_once("website.php");

LibHtml::preventCaching();


$formItemIds = LibEnv::getEnvHttpPOST("formItemIds");

$listOrder = 1;
foreach ($formItemIds as $formItemId) {
  // An ajax request parameter value is UTF-8 encoded
  $formItemId = utf8_decode($formItemId);

  if ($formItem = $formItemUtils->selectById($formItemId)) {
    $formItem->setListOrder($listOrder);
    $formItemUtils->update($formItem);
    $listOrder++;
  }
}

?>
