<?PHP

require_once("website.php");

LibHtml::preventCaching();

$peopleCategoryIds = LibEnv::getEnvHttpPOST("peopleCategoryIds");

$listOrder = 1;
foreach ($peopleCategoryIds as $peopleCategoryId) {
  // An ajax request parameter value is UTF-8 encoded
  $peopleCategoryId = utf8_decode($peopleCategoryId);

  if ($peopleCategory = $peopleCategoryUtils->selectById($peopleCategoryId)) {
    $peopleCategory->setListOrder($listOrder);
    $peopleCategoryUtils->update($peopleCategory);
    $listOrder++;
  }
}

?>
