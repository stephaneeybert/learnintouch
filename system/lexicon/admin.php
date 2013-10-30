<?PHP

require_once("website.php");

$mlText = $languageUtils->getMlText(__FILE__);

$lexiconSelectUrl = LibSession::getSessionValue(LEXICON_SESSION_SELECT_URL);
$panelUtils->setHeader($mlText[0], $lexiconSelectUrl);
$help = $popupUtils->getHelpPopup($mlText[5], 300, 200);
$panelUtils->setHelp($help);

$searchPattern = LibEnv::getEnvHttpPOST("searchPattern");
$searchSubmitted = LibEnv::getEnvHttpPOST("searchSubmitted");
if (!$searchPattern && !$searchSubmitted) {
  $searchPattern = LibSession::getSessionValue(LEXICON_SESSION_SEARCH_PATTERN);
} else {
  LibSession::putSessionValue(LEXICON_SESSION_SEARCH_PATTERN, $searchPattern);
}
$searchPattern = LibString::cleanString($searchPattern);

$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[7], $mlText[8], 300, 300);
$strSearch = "<input type='text' name='searchPattern' size='20' maxlength='50' value='$searchPattern'> "
  . "<input type='hidden' name='searchSubmitted' value='1'> "
  . $panelUtils->getTinyOk();
$strCommand = " <a href='$gLexiconUrl/preference.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageSetup' title='$mlText[15]'></a>";
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $panelUtils->addCell($strSearch, "n"), '', $panelUtils->addCell($strCommand, "nr"));
$panelUtils->closeForm();
$panelUtils->addLine();

// Get the number of items
$nbLexiconEntries = $lexiconEntryUtils->countAll();

$strCommand = "<a href='$gLexiconUrl/edit.php' $gJSNoStatus>"
. "<img border='0' src='$gCommonImagesUrl/$gImageAdd' title='$mlText[6]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[1], "nb"), $panelUtils->addCell($mlText[2], "nb"), $panelUtils->addCell($mlText[9], "nb"), $panelUtils->addCell($strCommand, "nbr"));

$preferenceUtils->init($adminUtils->preferences);
$listStep = $preferenceUtils->getValue("ADMIN_LIST_STEP");
$listIndex = LibEnv::getEnvHttpPOST("listIndex");
if (LibString::isEmpty($listIndex)) {
  $listIndex = LibEnv::getEnvHttpGET("listIndex");
}

if ($searchPattern) {
  $lexiconEntries = $lexiconEntryUtils->selectLikePattern($searchPattern, $listIndex, $listStep);
} else {
  $lexiconEntries = $lexiconEntryUtils->selectAll($listIndex, $listStep);
}

$listNbItems = $lexiconEntryUtils->countFoundRows();
$paginationUtils = new PaginationUtils($listNbItems, $listStep, $listIndex);
$paginationLinks = $paginationUtils->render();
if ($paginationLinks) {
  $panelUtils->addLine($paginationLinks);
} else {
  $panelUtils->addLine();
}

$panelUtils->openList();
foreach ($lexiconEntries as $lexiconEntry) {
  $lexiconEntryId = $lexiconEntry->getId();
  $name = $lexiconEntry->getName();
  $image = $lexiconEntry->getImage();
  $explanation = $lexiconEntry->getExplanation();

  if ($image) {
    $strImage = "<img src='" . $lexiconEntryUtils->imageFileUrl . '/' . $image . "' border='0' href='' title='$image'>";
  } else {
    $strImage = '';
  }

  $strCommand = "<a href='$gLexiconUrl/edit.php?lexiconEntryId=$lexiconEntryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageEdit' title='$mlText[3]'></a>"
    . ' ' . $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[14]'>", "$gLexiconUrl/image.php?lexiconEntryId=$lexiconEntryId", 600, 600)
    . " <a href='$gLexiconUrl/delete.php?lexiconEntryId=$lexiconEntryId' $gJSNoStatus>"
    . "<img border='0' src='$gCommonImagesUrl/$gImageDelete' title='$mlText[4]'></a>";

  $panelUtils->addLine($name, $explanation, $strImage, $panelUtils->addCell($strCommand, "nbr"));

}
$panelUtils->closeList();

$strRememberScroll = LibJavaScript::rememberScroll("lexicon_admin_vscroll");
$panelUtils->addContent($strRememberScroll);

$str = $panelUtils->render();

printAdminPage($str);

?>
