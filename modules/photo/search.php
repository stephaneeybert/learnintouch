<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted == 1) {

  $reference = LibEnv::getEnvHttpPOST("reference");
  $pattern = LibEnv::getEnvHttpPOST("pattern");
  $publication = LibEnv::getEnvHttpPOST("publication");

  $reference = LibString::cleanString($reference);
  $pattern = LibString::cleanString($pattern);
  $publication = LibString::cleanString($publication);

  LibSession::putSessionValue(PHOTO_SESSION_REFERENCE, $reference);
  LibSession::putSessionValue(PHOTO_SESSION_SEARCH_PATTERN, $pattern);
  LibSession::putSessionValue(PHOTO_SESSION_PUBLICATION_DATE, $publicationDate);

  if (count($warnings) == 0) {
    $publicationDate = '';
    if (trim($publication)) {
      $systemDate = $clockUtils->getSystemDate();
      $publicationDate = $clockUtils->incrementDays($systemDate, -1 * $publication);
    }

    $str = $photoAlbumUtils->renderSearchList($reference, $pattern, $publicationDate);

    $gTemplate->setPageContent($str);
    require_once($gTemplatePath . "render.php");
    exit;
  }

} else {

  $reference = LibSession::getSessionValue(PHOTO_SESSION_REFERENCE);
  $pattern = LibSession::getSessionValue(PHOTO_SESSION_SEARCH_PATTERN);
  $publicationDate = LibSession::getSessionValue(PHOTO_SESSION_PUBLICATION_DATE);

}

$listPublicationDates = array(
  '',
  7 => $websiteText[43],
  30 => $websiteText[44],
  90 => $websiteText[45],
  180 => $websiteText[46],
  365 => $websiteText[47],
);
$strSelectAvailable = LibHtml::getSelectList("publicationDate", $listPublicationDates, $publicationDate);

$str = '';

$str .= "\n<div class='system'>";

$str .= $commonUtils->renderWarningMessages($warnings);

$strViewList = "\n<a href='$gPhotoUrl/display_list.php' $gJSNoStatus title='" .  $websiteText[29] . "'>"
  . "<img src='$gImagesUserUrl/" . IMAGE_PHOTO_ALBUM_LIST . "' class='no_style_image_icon' title='' alt='' />" . "</a>";

$str .= "\n<div class='system_icons'>"
  . "\n $strViewList"
  . "\n</div>";

$str .= "\n<form id='photo_search' name='photo_search' action='$gPhotoUrl/search.php' method='post'>";

$label = $popupUtils->getUserTipPopup($websiteText[13], $websiteText[14], 300, 200);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='pattern' size='25' maxlength='50' value='$pattern' /></div>";

$label = $popupUtils->getUserTipPopup($websiteText[5], $websiteText[15], 300, 200);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'><input class='system_input' type='text' name='reference' size='25' maxlength='50' value='$reference' /></div>";

$label = $popupUtils->getUserTipPopup($websiteText[48], $websiteText[16], 300, 200);
$str .= "\n<div class='system_label'>$label</div>";
$str .= "\n<div class='system_field'>$strSelectAvailable</div>";

$str .= "\n<div class='photo_send_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['photo_search'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[0]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";

$str .= "\n</form>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
