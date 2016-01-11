<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_PHOTO);

$mlText = $languageUtils->getMlText(__FILE__);


$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $photoId = LibEnv::getEnvHttpPOST("photoId");
  $reference = LibEnv::getEnvHttpPOST("reference");
  $name = LibEnv::getEnvHttpPOST("name");
  $description = LibEnv::getEnvHttpPOST("description");
  $tags = LibEnv::getEnvHttpPOST("tags");
  $comment = LibEnv::getEnvHttpPOST("comment");
  $photoAlbumId = LibEnv::getEnvHttpPOST("photoAlbumId");
  $photoFormatId = LibEnv::getEnvHttpPOST("photoFormatId");
  $url = LibEnv::getEnvHttpPOST("url");
  $price = LibEnv::getEnvHttpPOST("price");

  $reference = LibString::cleanString($reference);
  $name = LibString::cleanString($name);
  $description = LibString::cleanString($description);
  $tags = LibString::cleanString($tags);
  $comment = LibString::cleanString($comment);
  $url = LibString::cleanString($url);
  $price = LibString::cleanString($price);

  // Validate the url
  if ($url && LibUtils::isInvalidUrl($url)) {
    array_push($warnings, $mlText[21]);
  }

  // Check that a photo album has been selected
  if(!$photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
    array_push($warnings, $mlText[15]);
  }

  // Strip the non numbers
  $price = LibString::stripNonNumbers($price);

  // Format the url
  $url = LibUtils::formatUrl($url);

  // If the photo is assigned to another album then the photo list order must be set according to the album number of photos
  // Otherwise the photo list order is not changed

  $listOrder = '';
  if ($photo = $photoUtils->selectById($photoId)) {
    $listOrder = $photo->getListOrder();
    $currentPhotoAlbumId = $photo->getPhotoAlbum();
    $image = $photo->getImage();
  }

  if (count($warnings) == 0) {

    // Check if the album is changed
    if ($currentPhotoAlbumId != $photoAlbumId) {
      // Get the next list order
      $listOrder = $photoUtils->getNextListOrder($photoAlbumId);

      // Move the photo to the new album
      $imagePath = $photoUtils->imagePath;
      if ($currentPhotoAlbum = $photoAlbumUtils->selectById($currentPhotoAlbumId)) {
        $currentFolderName = $currentPhotoAlbum->getFolderName();
        if ($photoAlbum = $photoAlbumUtils->selectById($photoAlbumId)) {
          $photoAlbumName = $photoAlbum->getName();
          $folderName = $photoAlbum->getFolderName();
          // If the destination directory does not exist for whatever reason then create it
          if (!file_exists($imagePath . $folderName)) {
            mkdir($imagePath . $folderName);
          }
          if (file_exists($imagePath . $currentFolderName . '/' . $image)) {
            rename($imagePath . $currentFolderName . '/' . $image, $imagePath . $folderName . '/' . $image);
          }
        }
      }
    }

    if ($photo = $photoUtils->selectById($photoId)) {
      $photo->setReference($reference);
      $photo->setName($name);
      $photo->setDescription($description);
      $photo->setTags($tags);
      $photo->setComment($comment);
      $photo->setPhotoAlbum($photoAlbumId);
      $photo->setPhotoFormatId($photoFormatId);
      $photo->setUrl($url);
      $photo->setPrice($price);
      $photo->setListOrder($listOrder);
      $photoUtils->update($photo);
    } else {
      $photo = new Photo();
      $photo->setReference($reference);
      $photo->setName($name);
      $photo->setDescription($description);
      $photo->setTags($tags);
      $photo->setComment($comment);
      $photo->setPhotoAlbum($photoAlbumId);
      $photo->setPhotoFormatId($photoFormatId);
      $photo->setUrl($url);
      $photo->setPrice($price);
      $photo->setListOrder($listOrder);
      $photoUtils->insert($photo);
      $photoId = $photoUtils->getLastInsertId();
    }

    $str = LibHtml::urlRedirect("$gPhotoUrl/admin.php");
    printContent($str);
    return;

  }

} else {

  $photoId = LibEnv::getEnvHttpGET("photoId");

  if ($photoId) {
    if ($photo = $photoUtils->selectById($photoId)) {
      $name = $photo->getName();
      $description = $photo->getDescription();
      $tags = $photo->getTags();
      $comment = $photo->getComment();
      $reference = $photo->getReference();
      $photoAlbumId = $photo->getPhotoAlbum();
      $photoFormatId = $photo->getPhotoFormatId();
      $url = $photo->getUrl();
      $price = $photo->getPrice();
    }
  }

}

$photoAlbums = $photoAlbumUtils->selectAll();
$photoAlbumList = Array('' => '');
foreach ($photoAlbums as $photoAlbum) {
  $wPhotoAlbumId = $photoAlbum->getId();
  $wName = $photoAlbum->getName();
  $photoAlbumList[$wPhotoAlbumId] = $wName;
}
$strSelect = LibHtml::getSelectList("photoAlbumId", $photoAlbumList, $photoAlbumId);

$photoFormats = $photoFormatUtils->selectAll();
$photoFormatList = Array('' => '');
foreach ($photoFormats as $photoFormat) {
  $wPhotoFormatId = $photoFormat->getId();
  $wName = $photoFormat->getName();
  $photoFormatList[$wPhotoFormatId] = $wName;
}
$strSelectFormat = LibHtml::getSelectList("photoFormatId", $photoFormatList, $photoFormatId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gPhotoUrl/admin.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$panelUtils->openForm($PHP_SELF);
$panelUtils->addLine($panelUtils->addCell($mlText[5], "nbr"), $strSelect);
$panelUtils->addLine();
$panelUtils->addLine($panelUtils->addCell($mlText[14], "nbr"), $strSelectFormat);
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='name'  value='$name' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[7], $mlText[3], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='description'  value='$description' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[16], $mlText[17], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='tags'  value='$tags' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[2], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea name='comment' cols='28' rows='7'>$comment</textarea>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[6], $mlText[10], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='reference' value='$reference' size='30' maxlength='50'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[8], $mlText[11], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='url' value='$url' size='30' maxlength='255'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[1], $mlText[4], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='price' value='$price' size='10' maxlength='10'>");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('photoId', $photoId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
