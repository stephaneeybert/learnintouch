<?PHP

require_once("website.php");

LibHtml::preventCaching();


$peopleIds = LibEnv::getEnvHttpPOST("peopleIds");

$listOrder = 1;
foreach ($peopleIds as $peopleId) {
  if ($people = $peopleUtils->selectById($peopleId)) {
    $people->setListOrder($listOrder);
    $peopleUtils->update($people);
    $listOrder++;
  }
}

?>
