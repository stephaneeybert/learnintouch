<?PHP

require_once("website.php");

LibHtml::preventCaching();


$linkIds = LibEnv::getEnvHttpPOST("linkIds");

$listOrder = 1;
foreach ($linkIds as $linkId) {
  // An ajax request parameter value is UTF-8 encoded
  $linkId = utf8_decode($linkId);

  if ($link = $linkUtils->selectById($linkId)) {
    $link->setListOrder($listOrder);
    $linkUtils->update($link);
    $listOrder++;
  }
}

?>
