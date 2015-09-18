<?PHP

require_once("website.php");

LibHtml::preventCaching();

$clientIds = LibEnv::getEnvHttpPOST("clientIds");

$listOrder = 1;
foreach ($clientIds as $clientId) {
  // An ajax request parameter value is UTF-8 encoded
  $clientId = utf8_decode($clientId);

  if ($client = $clientUtils->selectById($clientId)) {
    $client->setListOrder($listOrder);
    $clientUtils->update($client);
    $listOrder++;
  }
}

?>
