<?PHP

if (!$gIsPhoneClient) {
  $gImagesUserPath = $imageSetUtils->computerImagePath;
  $gImagesUserUrl = $imageSetUtils->computerImageUrl;
} else {
  $gImagesUserPath = $imageSetUtils->phoneImagePath;
  $gImagesUserUrl = $imageSetUtils->phoneImageUrl;
}

?>
