<?php

require_once("website.php");

$imagePath = $dynpageUtils->imagePath;
$imageUrl = $dynpageUtils->imageUrl;
$imageSize = $dynpageUtils->imageSize;

$filePath = $flashUtils->filePath;
$fileUrl = $flashUtils->fileUrl;
$fileSize = $flashUtils->fileSize;

include($gInnovaHtmlEditorPath . "image.php");

?>
