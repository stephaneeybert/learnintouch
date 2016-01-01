<?php

require_once("website.php");

// Get the path to the image directory
$imagePath = $newsStoryImageUtils->imageFilePath;
$imageUrl = $newsStoryImageUtils->imageFileUrl;
$imageSize = $newsStoryImageUtils->imageFileSize;
$imageWidth = $newsStoryUtils->getImageWidth();

include($gSystemPath . "editor/ckeditor/connector/image.php");

?>
