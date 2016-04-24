<?PHP

require_once("website.php");

LibHtml::preventCaching();

$linkIds = LibEnv::getEnvHttpPOST("linkIds");

$listOrder = 1;
foreach ($linkIds as $linkId) {
  if ($link = $linkUtils->selectById($linkId)) {
    $link->setListOrder($listOrder);
    $linkUtils->update($link);
    $listOrder++;
  }
}

?>
