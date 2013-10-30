<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[4]);
  }

  if (count($warnings) == 0) {

    if ($newsHeading = $newsHeadingUtils->selectById($newsHeadingId)) {
      $newsHeading->setName($name);
      $newsHeading->setDescription($description);
      $newsHeading->setNewsPublicationId($newsPublicationId);
      $newsHeadingUtils->update($newsHeading);
    } else {
      $newsHeading = new NewsHeading();
      $newsHeading->setListOrder($newsHeadingUtils->getNextListOrder($newsPublicationId));
      $newsHeading->setName($name);
      $newsHeading->setDescription($description);
      $newsHeading->setNewsPublicationId($newsPublicationId);
      $newsHeadingUtils->insert($newsHeading);
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsHeading/admin.php");
    printContent($str);
    return;

  }

} else {

  $newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");

  $name = '';
  $description = '';
  $newsPublicationId = '';
  if ($newsHeadingId) {
    if ($newsHeading = $newsHeadingUtils->selectById($newsHeadingId)) {
      $name = $newsHeading->getName();
      $description = $newsHeading->getDescription();
      $newsPublicationId = $newsHeading->getNewsPublicationId();
    }
  }

}

$newsPublications = $newsPublicationUtils->selectAll();
$newsPublicationList = Array('' => '');
foreach ($newsPublications as $newsPublication) {
  $wId = $newsPublication->getId();
  $wName = $newsPublication->getName();
  $newsPublicationList[$wId] = $wName;
}
$strSelectNewsPublication = LibHtml::getSelectList("newsPublicationId", $newsPublicationList, $newsPublicationId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsHeading/admin.php");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
// Allowing a heading to be moved into another publication puts the data in an incoherent state
// as the news stories of the publication's newspaper will not be sortable since their heading as been
// moved into another publication
if (!$newsHeadingId) {
  $panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelectNewsPublication);
  $panelUtils->addLine();
}
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[7], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsHeadingId', $newsHeadingId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
