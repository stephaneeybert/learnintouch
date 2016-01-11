<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
  $name = LibEnv::getEnvHttpPOST("name");
  $currentFolderName = LibEnv::getEnvHttpPOST("currentFolderName");
  $event = LibEnv::getEnvHttpPOST("event");
  $location = LibEnv::getEnvHttpPOST("location");
  $publicationDate = LibEnv::getEnvHttpPOST("publicationDate");
  $price = LibEnv::getEnvHttpPOST("price");
  $hide = LibEnv::getEnvHttpPOST("hide");
  $noSlideShow = LibEnv::getEnvHttpPOST("noSlideShow");
  $noZoom = LibEnv::getEnvHttpPOST("noZoom");

  $folderName = LibString::stripNonFilenameChar($name);
  $name = LibString::cleanString($name);
  $event = LibString::cleanString($event);
  $location = LibString::cleanString($location);
  $publicationDate = LibString::cleanString($publicationDate);
  $price = LibString::cleanString($price);
  $hide = LibString::cleanString($hide);
  $noSlideShow = LibString::cleanString($noSlideShow);
  $noZoom = LibString::cleanString($noZoom);

  // The name is required
  if (!$name) {
    array_push($warnings, $mlText[6]);
  }

  // Validate the creation date
  if ($clockUtils->systemDateIsSet($publicationDate) && !$clockUtils->isLocalNumericDateValid($publicationDate)) {
    array_push($warnings, $mlText[21] . " " . $clockUtils->getDateNumericFormatTip());
  }

  if ($clockUtils->systemDateIsSet($publicationDate)) {
    $publicationDate = $clockUtils->localToSystemDate($publicationDate);
  }

  // The name must be already used by another album
  if ($photoAlbum = $photoAlbumUtils->selectByName($name)) {
    $wPhotoAlbumId = $photoAlbum->getId();
    if ($wPhotoAlbumId != $photoAlbumId) {
      array_push($warnings, $mlText[2]);
    }
  }

  if (count($warnings) == 0) {

    // Rename or create the directory of the album
    $imagePath = $photoUtils->imagePath;
    if (!$currentFolderName) {
      $currentFolderName = $folderName;
    }
    if ($currentFolderName && file_exists($imagePath . $currentFolderName)) {
      rename($imagePath . $currentFolderName, $imagePath . $folderName);
    } else if (!file_exists($imagePath . $folderName)) {
      mkdir($imagePath . $folderName);
    }

    if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
      $photoAlbum->setName($name);
      $photoAlbum->setFolderName($folderName);
      $photoAlbum->setEvent($event);
      $photoAlbum->setLocation($location);
      $photoAlbum->setPublicationDate($publicationDate);
      $photoAlbum->setPrice($price);
      $photoAlbum->setHide($hide);
      $photoAlbum->setNoSlideShow($noSlideShow);
      $photoAlbum->setNoZoom($noZoom);
      $photoAlbumUtils->update($photoAlbum);
    } else {
      $photoAlbum = new PhotoAlbum();
      $photoAlbum->setName($name);
      $photoAlbum->setFolderName($folderName);
      $photoAlbum->setEvent($event);
      $photoAlbum->setLocation($location);
      $photoAlbum->setPublicationDate($publicationDate);
      $photoAlbum->setPrice($price);
      $photoAlbum->setHide($hide);
      $photoAlbum->setNoSlideShow($noSlideShow);
      $photoAlbum->setNoZoom($noZoom);

      // Get the next list order if a photo album is specified
      $nextListOrder = $photoAlbumUtils->getNextListOrder();
      $photoAlbum->setListOrder($nextListOrder);

      $photoAlbumUtils->insert($photoAlbum);
    }

    $str = LibHtml::urlRedirect("$gPhotoUrl/album/admin.php");
    printContent($str);
    return;

  }

} else {

  $photoAlbumId = LibEnv::getEnvHttpGET("photoAlbumId");

  $name = '';
  $folderName = '';
  $event = '';
  $location = '';
  $publicationDate = '';
  $price = '';
  $hide = '';
  $noSlideShow = '';
  $noZoom = '';
  if ($photoAlbumId) {
    if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
      $name = $photoAlbum->getName();
      $folderName = $photoAlbum->getFolderName();
      $event = $photoAlbum->getEvent();
      $location = $photoAlbum->getLocation();
      $publicationDate = $photoAlbum->getPublicationDate();
      $price = $photoAlbum->getPrice();
      $hide = $photoAlbum->getHide();
      $noSlideShow = $photoAlbum->getNoSlideShow();
      $noZoom = $photoAlbum->getNoZoom();
    }
  }

}

if ($hide == '1') {
  $checkedHide = "CHECKED";
} else {
  $checkedHide = '';
}

if ($noSlideShow == '1') {
  $checkedNoSlideShow = "CHECKED";
} else {
  $checkedNoSlideShow = '';
}

if ($noZoom == '1') {
  $checkedNoZoom = "CHECKED";
} else {
  $checkedNoZoom = '';
}

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$publicationDate = $clockUtils->systemToLocalNumericDate($publicationDate);

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/album/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF, "edit");
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<input type='text' name='name' value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), "<input type='text' name='event' value='$event' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[3], "nbr"), "<input type='text' name='location' value='$location' size='30' maxlength='255'>");
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[8], "nbr"), "<input type='text' name='publicationDate' id='publicationDate' value='$publicationDate' size='12' maxlength='10'> " . $clockUtils->getDateNumericFormatTip());
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[9], $mlText[7], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='price' value='$price' size='10' maxlength='10'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='hide' $checkedHide value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='noSlideShow' $checkedNoSlideShow value='1'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[14], $mlText[15], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='noZoom' $checkedNoZoom value='1'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoAlbumId', $photoAlbumId);
$panelUtils->addHiddenField('currentFolderName', $folderName);
$panelUtils->closeForm();

if ($clockUtils->isUSDateFormat()) {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#publicationDate").datepicker({ dateFormat:'mm/dd/yy' });
});
</script>
HEREDOC;
} else {
  $strJsSuggestCloseDate = <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $("#publicationDate").datepicker({ dateFormat:'dd-mm-yy' });
});
</script>
HEREDOC;
}

$languageCode = $languageUtils->getCurrentAdminLanguageCode();
$code = LibJavaScript::renderJQueryDatepickerLanguageCode($languageCode);
$strJsSuggestCloseDate .= <<<HEREDOC
<script type='text/javascript'>
$(function() {
  $.datepicker.setDefaults($.datepicker.regional['$code']);
});
</script>
HEREDOC;
$panelUtils->addContent($strJsSuggestCloseDate);

$str = $panelUtils->render();

printAdminPage($str);

?>
