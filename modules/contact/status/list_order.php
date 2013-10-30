<?PHP

require_once("website.php");

LibHtml::preventCaching();


$contactStatusIds = LibEnv::getEnvHttpPOST("contactStatusIds");

$listOrder = 1;
foreach ($contactStatusIds as $contactStatusId) {
  // An ajax request parameter value is UTF-8 encoded
  $contactStatusId = utf8_decode($contactStatusId);

  if ($contactStatus = $contactStatusUtils->selectById($contactStatusId)) {
    $contactStatus->setListOrder($listOrder);
    $contactStatusUtils->update($contactStatus);
    $listOrder++;
  }
}

?>
