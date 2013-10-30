<?

class ElearningExercisePdf extends PdfUtils {

  var $mlText;

  var $languageUtils;
  var $adminUtils;
  var $lexiconEntryUtils;
  var $elearningExerciseUtils;
  var $elearningExercisePageUtils;
  var $elearningExercisePagePdf;

  function ElearningExercisePdf() {
  }

  function loadLanguageTexts() {
    if ($this->adminUtils->getLoggedAdminId()) {
      $this->mlText = $this->languageUtils->getMlText(__FILE__, false);
    } else {
      $this->mlText = $this->languageUtils->getWebsiteText(__FILE__, false);
    }
  }

  function renderHeaderName(&$pdfDocument, $name) {
    $pdfDocument->SetY(PDF_LINE_HEIGHT);
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(128);
    $pdfDocument->MultiCell(0, PDF_LINE_HEIGHT, $name, 0, 'L');
  }

  function renderName(&$pdfDocument, $name) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', 'B', 14);
    $pdfDocument->SetTextColor(0);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $name, 0, 'C');
  }

  function renderDescription(&$pdfDocument, $description) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(0);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $description, 0, 'C');
  }

  function renderExerciseImage(&$pdfDocument, $image) {
    $pdfDocument->Ln();
    $y = $pdfDocument->GetY();
    $imageWidth = LibImage::getWidth($image);
    $x = $this->getImageCenterPosition($imageWidth);
    $pdfDocument->Image($image, $x, $y, '', '', '', '');
    $imageHeight = $this->getImageHeightInMm(LibImage::getHeight($image));
    $pdfDocument->Ln($imageHeight);
  }

  function renderInstructions(&$pdfDocument, $instructions) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(128);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $instructions, 0, 'C');
  }

  function renderIntroduction(&$pdfDocument, $text) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(0);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $text, 0, 'L');
  }

  function render(&$pdfDocument, $elearningExercise) {
    $this->loadLanguageTexts();

    $name = $elearningExercise->getName();
    $name = $this->pdfCleanString($name);
    if (strlen($name) > 40) {
      $shortName = substr($name, 0, 40) . '...';
    } else {
      $shortName = $name;
    }
    $this->renderHeaderName($pdfDocument, $this->mlText[1] . ' "' . $shortName . '"');

    $description = $elearningExercise->getDescription();
    $description = $this->pdfCleanString($description);
    $image = $elearningExercise->getImage();

    $this->renderName($pdfDocument, $name);

    if ($description) {
      $this->renderDescription($pdfDocument, $description);
    }

    $imagePath = $this->elearningExerciseUtils->imageFilePath;
    if ($image && is_file($imagePath . $image)) {
      $filename = LibImage::getJpgImage($imagePath, $image);
      if ($filename) {
        $this->renderExerciseImage($pdfDocument, $filename);
      }
    }

    $instructions = $this->elearningExerciseUtils->getStartInstructions($elearningExercise);
    $instructions = $this->pdfCleanString($instructions);
    if ($instructions) {
      $this->renderInstructions($pdfDocument, $instructions);
    }

    $introduction = $elearningExercise->getIntroduction();
    if ($introduction) {
      $cleanedUpIntroduction = $this->pdfCleanString($introduction);
      $this->renderIntroduction($pdfDocument, $cleanedUpIntroduction);

      if ($this->elearningExerciseUtils->displayLexiconList()) {
        $lexiconEntries = $this->lexiconEntryUtils->getLexiconTooltipsFromContent($introduction);
        if (count($lexiconEntries) > 0) {
          $pdfDocument->Ln();
          $pdfDocument->SetTextColor(0);
          foreach ($lexiconEntries as $lexiconEntry) {
            list($lexiconEntryId, $name, $explanation, $image) = $lexiconEntry;
            if ($explanation) {
              $pdfDocument->SetFont('Arial', 'B', 12);
              $name = $this->pdfCleanString($name);
              $pdfDocument->Write($pdfDocument->lineHeight, '- ' . $name . ': ');
              $pdfDocument->SetFont('Arial', '', 12);
              $explanation = $this->pdfCleanString($explanation);
              $explanation = LibString::addTraillingChar($explanation, '.');
              $pdfDocument->Write($pdfDocument->lineHeight, $explanation . ' ');
            }
          }
        }
      }
    }

    $elearningExercisePages = $this->elearningExercisePageUtils->selectByExerciseId($elearningExercise->getId());
    foreach ($elearningExercisePages as $elearningExercisePage) {
      $pdfDocument->AddPage();
      $this->elearningExercisePagePdf->render($pdfDocument, $elearningExercisePage);
    }

    if ($this->elearningExerciseUtils->printSolutionsOnSeparatePage($elearningExercise->getId())) {
      foreach ($elearningExercisePages as $elearningExercisePage) {
        $pdfDocument->AddPage();
        $this->elearningExercisePagePdf->renderSolutionsPage($pdfDocument, $elearningExercisePage);
      }
    }
  }

}

?>
