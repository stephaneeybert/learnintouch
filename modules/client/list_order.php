<?PHP

require_once("website.php");

LibHtml::preventCaching();

$clientIds = LibEnv::getEnvHttpPOST("clientIds");

$listOrder = 1;
foreach ($clientIds as $clientId) {
  if ($client = $clientUtils->selectById($clientId)) {
    $client->setListOrder($listOrder);
    $clientUtils->update($client);
    $listOrder++;
  }
}

?>
