<?PHP

require_once("website.php");

LibHtml::preventCaching();


$contactStatusIds = LibEnv::getEnvHttpPOST("contactStatusIds");

$listOrder = 1;
foreach ($contactStatusIds as $contactStatusId) {
  if ($contactStatus = $contactStatusUtils->selectById($contactStatusId)) {
    $contactStatus->setListOrder($listOrder);
    $contactStatusUtils->update($contactStatus);
    $listOrder++;
  }
}

?>
