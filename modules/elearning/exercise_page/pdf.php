<?php

require_once("website.php");

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

// Prevent sql injection attacks as the id is always numeric
$elearningExercisePageId = (int) $elearningExercisePageId;

if (!$elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;
}

$websiteName = $profileUtils->getWebSiteName();
$copyright = $profileUtils->getWebSiteCopyright();

// The pdf filename
$filename = $elearningExercisePage->getName();
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
    $elearningExercisePagePdf->renderLogo($pdfDocument, $profileUtils->filePath . $logo, $gSetupWebsiteUrl);
  }
}

$elearningExerciseId = $elearningExercisePage->getElearningExerciseId();

$elearningExercisePagePdf->render($pdfDocument, $elearningExercisePage);

if ($elearningExerciseUtils->printSolutionsOnSeparatePage($elearningExerciseId)) {
  $pdfDocument->AddPage();
  $elearningExercisePagePdf->renderSolutionsPage($pdfDocument, $elearningExercisePage);
}


$pdfDocument->Output($pdfFilename, 'I');

?>
