<?php

require_once("website.php");

// Get the path to the image directory
$imagePath = $templateUtils->imagePath;
$imageUrl = $templateUtils->imageUrl;
$imageSize = $templateUtils->imageSize;

// Get the path to the Flash directory
$filePath = $flashUtils->filePath;
$fileUrl = $flashUtils->fileUrl;
$fileSize = $flashUtils->fileSize;

include($gSystemPath . "editor/ckeditor/connector/image.php");

?>
