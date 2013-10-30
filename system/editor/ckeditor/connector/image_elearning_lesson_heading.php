<?php

require_once("website.php");

// Get the path to the image directory
$imagePath = $elearningLessonHeadingUtils->imageFilePath;
$imageUrl = $elearningLessonHeadingUtils->imageFileUrl;
$imageSize = $elearningLessonHeadingUtils->imageFileSize;

// Get the path to the Flash directory
$filePath = $flashUtils->filePath;
$fileUrl = $flashUtils->fileUrl;
$fileSize = $flashUtils->fileSize;

include($gSystemPath . "editor/ckeditor/connector/image.php");

?>
