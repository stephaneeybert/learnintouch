<?php

require_once("website.php");

// Get the path to the image directory
$imagePath = $elearningLessonUtils->imageFilePath;
$imageUrl = $elearningLessonUtils->imageFileUrl;
$imageSize = $elearningLessonUtils->imageFileSize;
$imageWidth = $elearningLessonUtils->getImageWidth();

// Get the path to the Flash directory
$filePath = $flashUtils->filePath;
$fileUrl = $flashUtils->fileUrl;
$fileSize = $flashUtils->fileSize;

include($gSystemPath . "editor/ckeditor/connector/image.php");

?>
