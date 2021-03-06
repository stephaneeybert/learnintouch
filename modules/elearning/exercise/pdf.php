<?php

require_once("website.php");

$elearningExerciseId = LibEnv::getEnvHttpGET("elearningExerciseId");

if ($elearningExercise = $elearningExerciseUtils->selectById($elearningExerciseId)) {

  $websiteName = $profileUtils->getWebSiteName();
  $copyright = $profileUtils->getWebSiteCopyright();

  $filename = $elearningExercise->getName();
  $filename = LibString::decodeHtmlspecialchars($filename);
  $pdfFilename = $filename . ".pdf";

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
      $elearningExercisePdf->renderLogo($pdfDocument, $profileUtils->filePath . $logo, $gSetupWebsiteUrl);
    }
  }

  $elearningExercisePdf->render($pdfDocument, $elearningExercise);

  $pdfDocument->Output($pdfFilename, 'I');

} else {

  $str = LibJavascript::autoCloseWindow();
  printContent($str);
  return;

}

?>
