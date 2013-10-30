<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);

$newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");

if (!$newsPublicationId) {
  $newsPublicationId = LibSession::getSessionValue(NEWS_SESSION_NEWSPUBLICATION);
} else {
  LibSession::putSessionValue(NEWS_SESSION_NEWSPUBLICATION, $newsPublicationId);
}

$newsPublications = $newsPublicationUtils->selectAll();
$newsPublicationList = Array('-1' => '');
foreach ($newsPublications as $newsPublication) {
  $wId = $newsPublication->getId();
  $wName = $newsPublication->getName();
  $newsPublicationList[$wId] = $wName;
}
$strSelectNewsPublication = LibHtml::getSelectList("newsPublicationId", $newsPublicationList, $newsPublicationId, true);

$panelUtils->setHeader($mlText[0], "$gNewsUrl/newsStory/admin.php");
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[11], "nbr"), $panelUtils->addCell($strSelectNewsPublication, "n"), '', '');
$panelUtils->closeForm();
$panelUtils->addLine();
$strCommand = "<a href='$gNewsUrl/newsHeading/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[1]'></a>";
$panelUtils->addLine("<B>$mlText[7]</B>", "<B>$mlText[8]</B>", "<B>$mlText[9]</B>", $panelUtils->addCell($strCommand, "nbr"));
$panelUtils->addLine();

$newsHeadings = array();
$newsHeadings = $newsHeadingUtils->selectByNewsPublicationId($newsPublicationId);

$panelUtils->openList();
foreach ($newsHeadings as $newsHeading) {
  $newsHeadingId = $newsHeading->getId();
  $name = $newsHeading->getName();
  $description = $newsHeading->getDescription();

  $strSwap = "<a href='$gNewsUrl/newsHeading/swapup.php?newsHeadingId=$newsHeadingId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageUp' title='$mlText[31]'></a>"
    . " <a href='$gNewsUrl/newsHeading/swapdown.php?newsHeadingId=$newsHeadingId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDown' title='$mlText[30]'></a>";

  $strCommand = "<a href='$gNewsUrl/newsHeading/edit.php?newsHeadingId=$newsHeadingId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[2]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[5]'>", "$gNewsUrl/newsHeading/image.php?newsHeadingId=$newsHeadingId", 600, 600)

    . " <a href='$gNewsUrl/newsHeading/delete.php?newsHeadingId=$newsHeadingId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[3]'></a>";

  $panelUtils->addLine($strSwap, $name, $description, $panelUtils->addCell($strCommand, "nr"));
}
$panelUtils->closeList();

$str = $panelUtils->render();

printAdminPage($str);

?>
