<?PHP

require_once("website.php");

LibHtml::preventCaching();

$peopleCategoryIds = LibEnv::getEnvHttpPOST("peopleCategoryIds");

$listOrder = 1;
foreach ($peopleCategoryIds as $peopleCategoryId) {
  if ($peopleCategory = $peopleCategoryUtils->selectById($peopleCategoryId)) {
    $peopleCategory->setListOrder($listOrder);
    $peopleCategoryUtils->update($peopleCategory);
    $listOrder++;
  }
}

?>
