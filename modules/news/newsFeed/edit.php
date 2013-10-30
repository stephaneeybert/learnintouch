<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_NEWS);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $newsFeedId = LibEnv::getEnvHttpPOST("newsFeedId");
  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
  $maxDisplayNumber = LibEnv::getEnvHttpPOST("maxDisplayNumber");
  $imageAlign = LibEnv::getEnvHttpPOST("imageAlign");
  $imageWidth = LibEnv::getEnvHttpPOST("imageWidth");
  $withExcerpt = LibEnv::getEnvHttpPOST("withExcerpt");
  $withImage = LibEnv::getEnvHttpPOST("withImage");
  $searchOptions = LibEnv::getEnvHttpPOST("searchOptions");
  $searchCalendar = LibEnv::getEnvHttpPOST("searchCalendar");
  $displayUpcoming = LibEnv::getEnvHttpPOST("displayUpcoming");
  $searchTitle = LibEnv::getEnvHttpPOST("searchTitle");
  $searchDisplayAsPage = LibEnv::getEnvHttpPOST("searchDisplayAsPage");

  $maxDisplayNumber = LibString::cleanString($maxDisplayNumber);
  $imageAlign = LibString::cleanString($imageAlign);
  $imageWidth = LibString::cleanString($imageWidth);
  $withExcerpt = LibString::cleanString($withExcerpt);
  $withImage = LibString::cleanString($withImage);
  $searchOptions = LibString::cleanString($searchOptions);
  $searchCalendar = LibString::cleanString($searchCalendar);
  $displayUpcoming = LibString::cleanString($displayUpcoming);
  $searchTitle = LibString::cleanString($searchTitle);
  $searchDisplayAsPage = LibString::cleanString($searchDisplayAsPage);

  // The newspaper is required
  if (!$newsPaperId) {
    array_push($warnings, $mlText[7]);
  }

  if (count($warnings) == 0) {

    if ($newsFeed = $newsFeedUtils->selectById($newsFeedId)) {
      $newsFeed->setNewsPaperId($newsPaperId);
      $newsFeed->setMaxDisplayNumber($maxDisplayNumber);
      $newsFeed->setImageAlign($imageAlign);
      $newsFeed->setImageWidth($imageWidth);
      $newsFeed->setWithExcerpt($withExcerpt);
      $newsFeed->setWithImage($withImage);
      $newsFeed->setSearchOptions($searchOptions);
      $newsFeed->setSearchCalendar($searchCalendar);
      $newsFeed->setDisplayUpcoming($displayUpcoming);
      $newsFeed->setSearchTitle($searchTitle);
      $newsFeed->setSearchDisplayAsPage($searchDisplayAsPage);
      $newsFeedUtils->update($newsFeed);
    }

    $str = LibJavascript::reloadParentWindow() . LibJavascript::autoCloseWindow();
    printContent($str);
    return;

  }

} else {

  $newsFeedId = LibEnv::getEnvHttpGET("newsFeedId");
  if (!$newsFeedId) {
    $newsFeedId = LibEnv::getEnvHttpPOST("newsFeedId");
  }

  $newsPublicationId = LibEnv::getEnvHttpPOST("newsPublicationId");

  $newsPaperId = '';
  if ($newsFeedId) {
    if ($newsFeed = $newsFeedUtils->selectById($newsFeedId)) {
      $newsPaperId = $newsFeed->getNewsPaperId();
      if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
        $newsPublicationId = $newsPaper->getNewsPublicationId();
      }
    }
  }

}

$image = '';
$maxDisplayNumber = '';
$imageAlign = '';
$imageWidth = '';
$withExcerpt = '';
$withImage = '';
$searchOptions = '';
$searchCalendar = '';
$displayUpcoming = '';
$searchTitle = '';
$searchDisplayAsPage = '';
if ($newsFeed = $newsFeedUtils->selectById($newsFeedId)) {
  $image = $newsFeed->getImage();
  $maxDisplayNumber = $newsFeed->getMaxDisplayNumber();
  $imageAlign = $newsFeed->getImageAlign();
  $imageWidth = $newsFeed->getImageWidth();
  $withExcerpt = $newsFeed->getWithExcerpt();
  $withImage = $newsFeed->getWithImage();
  $searchOptions = $newsFeed->getSearchOptions();
  $searchCalendar = $newsFeed->getSearchCalendar();
  $displayUpcoming = $newsFeed->getDisplayUpcoming();
  $searchTitle = $newsFeed->getSearchTitle();
  $searchDisplayAsPage = $newsFeed->getSearchDisplayAsPage();
}

$newsPublications = $newsPublicationUtils->selectAll();
$newsPublicationList = Array('' => '');
foreach ($newsPublications as $newsPublication) {
  $wId = $newsPublication->getId();
  $wName = $newsPublication->getName();
  $newsPublicationList[$wId] = $wName;
}
$strSelectNewsPublication = LibHtml::getSelectList("newsPublicationId", $newsPublicationList, $newsPublicationId, true);

$newsPaperList = array();
if ($newsPublicationId) {
  $newsPaperList = $newsPaperUtils->getPublicationNewsPaperList($newsPublicationId);
}
$strSelectNewsPaper = LibHtml::getSelectList("newsPaperId", $newsPaperList, $newsPaperId);

$maxDisplayNumberList = array();
for ($i = 0; $i < 50; $i++) {
  $maxDisplayNumberList[$i] = $i;
}
$strSelectMaxDisplayNumber = LibHtml::getSelectList("maxDisplayNumber", $maxDisplayNumberList, $maxDisplayNumber);

$imageUrl = $newsFeedUtils->imageUrl;
$imagePath = $newsFeedUtils->imagePath;
if ($image && is_file($imagePath . $image)) {
  $strImage = "<img src='$imageUrl/$image' border='0' title=''>";
} else {
  $strImage = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

if ($withExcerpt == '1') {
  $checkedWithExcerpt = "CHECKED";
} else {
  $checkedWithExcerpt = '';
}

if ($withImage == '1') {
  $checkedWithImage = "CHECKED";
} else {
  $checkedWithImage = '';
}

if ($searchOptions == '1') {
  $checkedSearchOptions = "CHECKED";
} else {
  $checkedSearchOptions = '';
}

if ($searchDisplayAsPage == '1') {
  $checkedSearchDisplayAsPage = "CHECKED";
} else {
  $checkedSearchDisplayAsPage = '';
}

if ($searchCalendar == '1') {
  $checkedSearchCalendar = "CHECKED";
} else {
  $checkedSearchCalendar = '';
}

if ($displayUpcoming == '1') {
  $checkedDisplayUpcoming = "CHECKED";
} else {
  $checkedDisplayUpcoming = '';
}

$imageAlignList = Array('0' => '', NEWS_ABOVE_HEADLINE => $mlText[16], NEWS_LEFT_CORNER_HEADLINE => $mlText[14], NEWS_RIGHT_CORNER_HEADLINE => $mlText[15], NEWS_ABOVE_EXCERPT => $mlText[19], NEWS_LEFT_CORNER_EXCERPT => $mlText[17], NEWS_RIGHT_CORNER_EXCERPT => $mlText[18]);
$strSelectImageAlign = LibHtml::getSelectList("imageAlign", $imageAlignList, $imageAlign);

$panelUtils->setHeader($mlText[0]);
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$label = $popupUtils->getTipPopup($mlText[3], $mlText[4], 300, 300);
$strCommand = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImagePicture' title='$mlText[5]'>", "$gNewsUrl/newsFeed/image.php?newsFeedId=$newsFeedId", 600, 600);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strCommand);
if ($strImage) {
  $panelUtils->addLine('', $strImage);
}
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[6], "nbr"), $strSelectNewsPublication);
$panelUtils->addLine();
$panelUtils->addHiddenField('newsFeedId', $newsFeedId);
$panelUtils->closeForm();
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[1], $mlText[2], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectNewsPaper);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectMaxDisplayNumber);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[26], $mlText[27], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='searchCalendar' $checkedSearchCalendar value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[24], $mlText[25], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='searchOptions' $checkedSearchOptions value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[32], $mlText[33], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='displayUpcoming' $checkedDisplayUpcoming value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[28], $mlText[29], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='searchDisplayAsPage' $checkedSearchDisplayAsPage value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[22], $mlText[23], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='searchTitle' value='$searchTitle' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[20], $mlText[21], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='imageWidth' value='$imageWidth' size='4' maxlength='4'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectImageAlign);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='withExcerpt' $checkedWithExcerpt value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[30], $mlText[31], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='withImage' $checkedWithImage value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('newsFeedId', $newsFeedId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
