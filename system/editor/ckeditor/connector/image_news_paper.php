<?php

require_once("website.php");

// Get the path to the image directory
$imagePath = $newsPaperUtils->imagePath;
$imageUrl = $newsPaperUtils->imageUrl;
$imageSize = $newsPaperUtils->imageSize;

include($gSystemPath . "editor/ckeditor/connector/image.php");

?>
