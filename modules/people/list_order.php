<?PHP

require_once("website.php");

LibHtml::preventCaching();


$peopleIds = LibEnv::getEnvHttpPOST("peopleIds");

$listOrder = 1;
foreach ($peopleIds as $peopleId) {
  // An ajax request parameter value is UTF-8 encoded
  $peopleId = utf8_decode($peopleId);

  if ($people = $peopleUtils->selectById($peopleId)) {
    $people->setListOrder($listOrder);
    $peopleUtils->update($people);
    $listOrder++;
  }
}

?>
