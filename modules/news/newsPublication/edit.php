<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $nbColumns = LibEnv::getEnvHttpPOST("nbColumns");
  $slideDown = LibEnv::getEnvHttpPOST("slideDown");
  $align = LibEnv::getEnvHttpPOST("align");
  $withArchive = LibEnv::getEnvHttpPOST("withArchive");
  $withOthers = LibEnv::getEnvHttpPOST("withOthers");
  $withByHeading = LibEnv::getEnvHttpPOST("withByHeading");
  $hideHeading = LibEnv::getEnvHttpPOST("hideHeading");
  $autoArchive = LibEnv::getEnvHttpPOST("autoArchive");
  $autoDelete = LibEnv::getEnvHttpPOST("autoDelete");
  $secured = LibEnv::getEnvHttpPOST("secured");

  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $nbColumns = LibString::cleanString($nbColumns);
  $slideDown = LibString::cleanString($slideDown);
  $align = LibString::cleanString($align);
  $withArchive = LibString::cleanString($withArchive);
  $withOthers = LibString::cleanString($withOthers);
  $withByHeading = LibString::cleanString($withByHeading);
  $hideHeading = LibString::cleanString($hideHeading);
  $autoArchive = LibString::cleanString($autoArchive);
  $autoDelete = LibString::cleanString($autoDelete);
  $secured = LibString::cleanString($secured);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[4]);
  }

  // Check that the period after which to delete the newspapers is greater than the one
  // after which to archive them
  // The newspapers should not be deleted unless they are first archived
  if ($autoDelete) {
    if ($autoDelete <= $autoArchive) {
      array_push($warnings, $mlText[8]);
    }
  }

  if (count($warnings) == 0) {

    if ($newsPublication = $newsPublicationUtils->selectById($newsPublicationId)) {
      $newsPublication->setName($name);
      $newsPublication->setDescription($description);
      $newsPublication->setNbColumns($nbColumns);
      $newsPublication->setSlideDown($slideDown);
      $newsPublication->setAlign($align);
      $newsPublication->setWithArchive($withArchive);
      $newsPublication->setWithOthers($withOthers);
      $newsPublication->setWithByHeading($withByHeading);
      $newsPublication->setHideHeading($hideHeading);
      $newsPublication->setAutoArchive($autoArchive);
      $newsPublication->setAutoDelete($autoDelete);
      $newsPublication->setSecured($secured);
      $newsPublicationUtils->update($newsPublication);
    } else {
      $newsPublication = new NewsPublication();
      $newsPublication->setName($name);
      $newsPublication->setDescription($description);
      $newsPublication->setNbColumns($nbColumns);
      $newsPublication->setSlideDown($slideDown);
      $newsPublication->setAlign($align);
      $newsPublication->setWithArchive($withArchive);
      $newsPublication->setWithOthers($withOthers);
      $newsPublication->setWithByHeading($withByHeading);
      $newsPublication->setHideHeading($hideHeading);
      $newsPublication->setAutoArchive($autoArchive);
      $newsPublication->setAutoDelete($autoDelete);
      $newsPublication->setSecured($secured);
      $newsPublicationUtils->insert($newsPublication);
    }

    $str = LibHtml::urlRedirect("$gNewsUrl/newsPublication/admin.php");
    printContent($str);
    return;

  }

} else {

  $newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");

  $name = '';
  $description = '';
  $nbColumns = '';
  $slideDown = '';
  $align = '';
  $withArchive = '';
  $withOthers = '';
  $withByHeading = '';
  $hideHeading = '';
  $autoArchive = '';
  $autoDelete = '';
  $secured = '';
  if ($newsPublicationId) {
    if ($newsPublication = $newsPublicationUtils->selectById($newsPublicationId)) {
      $name = $newsPublication->getName();
      $description = $newsPublication->getDescription();
      $nbColumns = $newsPublication->getNbColumns();
      $slideDown = $newsPublication->getSlideDown();
      $align = $newsPublication->getAlign();
      $withArchive = $newsPublication->getWithArchive();
      $withOthers = $newsPublication->getWithOthers();
      $withByHeading = $newsPublication->getWithByHeading();
      $hideHeading = $newsPublication->getHideHeading();
      $autoArchive = $newsPublication->getAutoArchive();
      $autoDelete = $newsPublication->getAutoDelete();
      $secured = $newsPublication->getSecured();
    }
  }

}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

if ($slideDown == '1') {
  $checkedSlideDown = "CHECKED";
} else {
  $checkedSlideDown = '';
}

if ($withArchive == '1') {
  $checkedWithArchive = "CHECKED";
} else {
  $checkedWithArchive = '';
}

if ($withOthers == '1') {
  $checkedWithOthers = "CHECKED";
} else {
  $checkedWithOthers = '';
}

if ($withByHeading == '1') {
  $checkedWithByHeading = "CHECKED";
} else {
  $checkedWithByHeading = '';
}

if ($hideHeading == '1') {
  $checkedHideHeading = "CHECKED";
} else {
  $checkedHideHeading = '';
}

if ($secured == '1') {
  $checkedSecured = "CHECKED";
} else {
  $checkedSecured = '';
}

$alignList = Array('0' => '', NEWS_ABOVE_HEADLINE => $mlText[11], NEWS_LEFT_CORNER_HEADLINE => $mlText[9], NEWS_RIGHT_CORNER_HEADLINE => $mlText[10], NEWS_ABOVE_EXCERPT => $mlText[22], NEWS_LEFT_CORNER_EXCERPT => $mlText[19], NEWS_RIGHT_CORNER_EXCERPT => $mlText[20]);
$strSelectImageAlign = LibHtml::getSelectList("align", $alignList, $align);

$nbColumnsList = Array('1' => 1, '2' => 2, '3' => 3, '4' => 4);
$strSelectNbColumns = LibHtml::getSelectList("nbColumns", $nbColumnsList, $nbColumns);

$autoArchiveList = Array('' => '', '7' => 7, '14' => 14, '30' => 30, '60' => 60, '90' => 90, '120' => 120, '180' => 180, '360' => 360);
$strSelectAutoArchive = LibHtml::getSelectList("autoArchive", $autoArchiveList, $autoArchive);

$autoDeleteList = Array('' => '', '30' => 30, '60' => 60, '90' => 90, '120' => 120, '180' => 180, '360' => 360);
$strSelectAutoDelete = LibHtml::getSelectList("autoDelete", $autoDeleteList, $autoDelete);

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsPublication/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), "<input type='text' name='description' value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[14], $mlText[21], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNbColumns);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[29], $mlText[30], 300, 100);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='slideDown' $checkedSlideDown value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[15], $mlText[16], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='withArchive' $checkedWithArchive value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[23], $mlText[24], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='withOthers' $checkedWithOthers value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[25], $mlText[26], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='withByHeading' $checkedWithByHeading value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[27], $mlText[28], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hideHeading' $checkedHideHeading value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[3], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectAutoArchive);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[6], $mlText[7], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectAutoDelete);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[17], $mlText[18], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectImageAlign);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='secured' $checkedSecured value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsPublicationId', $newsPublicationId);
$panelUtils->closeForm();

$str = $panelUtils->render();

printAdminPage($str);

?>
