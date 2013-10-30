<?php

require_once("website.php");

$elearningLessonId = LibEnv::getEnvHttpGET("elearningLessonId");

if (!$elearningLesson = $elearningLessonUtils->selectById($elearningLessonId)) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$websiteName = $profileUtils->getWebSiteName();
$copyright = $profileUtils->getWebSiteCopyright();

// The pdf filename
$filename = $elearningLesson->getName();
$filename = LibString::decodeHtmlspecialchars($filename);
$pdfFilename = $filename . ".pdf";

// Create the pdf
$pdfDocument = new PdfTemplate('P', 'mm', 'A4');
$pdfDocument->Open();
$pdfDocument->SetFont('Arial', '', 12);
$pdfDocument->AliasNbPages("totalNbPages");
$pdfDocument->setLineHeight(8);

$pdfDocument->setWebsiteName($websiteName);
$pdfDocument->setCopyright($copyright);
$localDate = $clockUtils->getLocalNumericDate();
$pdfDocument->setLocalDate($localDate);

$pdfDocument->AddPage();

if ($elearningExerciseUtils->displayWebsiteLogo()) {
  $logo = $profileUtils->getLogoFilename();
  if ($logo && is_file($profileUtils->filePath . $logo)) {
    $elearningLessonPdf->renderLogo($pdfDocument, $profileUtils->filePath . $logo, $gSetupWebsiteUrl);
  }
}

$elearningLessonPdf->render($pdfDocument, $elearningLesson);

// Create the pdf document
$pdfDocument->Output($pdfFilename, 'I');

?>
