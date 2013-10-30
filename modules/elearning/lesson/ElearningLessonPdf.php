<?

class ElearningLessonPdf extends PdfUtils {

  var $mlText;

  var $languageUtils;
  var $adminUtils;
  var $elearningLessonUtils;
  var $elearningLessonParagraphUtils;
  var $elearningExerciseUtils;
  var $lexiconEntryUtils;

  function ElearningLessonPdf() {
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
    $pdfDocument->SetFont('Arial', 'B', 14);
    $pdfDocument->SetTextColor(0);
    $pdfDocument->Ln();
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $name, 0, 'C');
  }

  function renderDescription(&$pdfDocument, $description) {
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->Ln();
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $description, 0, 'C');
  }

  function renderLessonImage(&$pdfDocument, $image) {
    $pdfDocument->Ln();
    $y = $pdfDocument->GetY();
    $imageWidth = LibImage::getWidth($image);
    $x = $this->getImageCenterPosition($imageWidth);
    $pdfDocument->Image($image, $x, $y, '', '', '', '');
    $imageHeight = $this->getImageHeightInMm(LibImage::getHeight($image));
    $pdfDocument->Ln($imageHeight);
  }

  function renderInstructions(&$pdfDocument, $instructions) {
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(128);
    $pdfDocument->Ln();
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $instructions, 0, 'C');
  }

  function renderIntroduction(&$pdfDocument, $introduction) {
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(000);
    $pdfDocument->Ln();
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $introduction, 0, 'L');
  }

  function renderParagraphImage(&$pdfDocument, $image) {
    $pdfDocument->Ln();
    $y = $pdfDocument->GetY();
    $imageWidth = LibImage::getWidth($image);
    $x = $this->getImageCenterPosition($imageWidth);
    $pdfDocument->Image($image, $x, $y, '', '', '', '');
    $imageHeight = $this->getImageHeightInMm(LibImage::getHeight($image));
    $pdfDocument->Ln($imageHeight);
  }

  function renderParagraphHeadline(&$pdfDocument, $headline) {
    $pdfDocument->SetFont('Arial', 'B', 12);
    $pdfDocument->Ln();
    $pdfDocument->Ln();
    $pdfDocument->Ln();
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $headline, 0, 'C');
  }

  function renderBody(&$pdfDocument, $body) {
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->Ln();
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $body, 0, 'L');
  }

  function renderParagraphExerciseLink(&$pdfDocument, $exerciseName, $exerciseUrl) {
    $pdfDocument->Ln();
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', 'B', 12);
    $pdfDocument->Write($pdfDocument->lineHeight, $this->mlText[2] . ' ');
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->Write($pdfDocument->lineHeight, $exerciseName, $exerciseUrl);
  }

  function render(&$pdfDocument, $elearningLesson) {
    global $gElearningUrl;

    $this->loadLanguageTexts();

    $name = $elearningLesson->getName();
    $name = $this->pdfCleanString($name);
    if (strlen($name) > 40) {
      $shortName = substr($name, 0, 40) . '...';
    } else {
      $shortName = $name;
    }
    $this->renderHeaderName($pdfDocument, $this->mlText[1] . ' "' . $shortName . '"');

    $description = $elearningLesson->getDescription();
    $description = $this->pdfCleanString($description);
    $image = $elearningLesson->getImage();

    $this->renderName($pdfDocument, $name);
    if ($description) {
      $this->renderDescription($pdfDocument, $description);
    }

    $imagePath = $this->elearningLessonUtils->imageFilePath;
    if ($image && is_file($imagePath . $image)) {
      $filename = LibImage::getJpgImage($imagePath, $image);
      if ($filename) {
        $this->renderLessonImage($pdfDocument, $filename);
      }
    }

    $instructions = $this->elearningLessonUtils->getInstructions($elearningLesson->getId());
    $instructions = $this->pdfCleanString($instructions);
    $this->renderInstructions($pdfDocument, $instructions);

    $introduction = $elearningLesson->getIntroduction();
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

    $paragraphs = $this->elearningLessonUtils->getLessonParagraphs($elearningLesson);
    foreach ($paragraphs as $paragraph) {
      list($elearningLessonParagraph, $headingName, $headingContent, $elearningLessonHeadingId) = $paragraph;
      $headline = $elearningLessonParagraph->getHeadline();
      $body = $elearningLessonParagraph->getBody();
      $image = $elearningLessonParagraph->getImage();
      $elearningExerciseId = $elearningLessonParagraph->getElearningExerciseId();
      $exerciseTitle = $elearningLessonParagraph->getExerciseTitle();

      $headline = $this->pdfCleanString($headline);
      $this->renderParagraphHeadline($pdfDocument, $headline);

      $imagePath = $this->elearningLessonParagraphUtils->imageFilePath;
      if ($image && is_file($imagePath . $image)) {
        $filename = LibImage::getJpgImage($imagePath, $image);
        if ($filename) {
          $this->renderParagraphImage($pdfDocument, $filename);
        }
      }

      if ($body) {
        $cleanedUpBody = $this->pdfCleanString($body);
        $this->renderBody($pdfDocument, $cleanedUpBody);

        if ($this->elearningExerciseUtils->displayLexiconList()) {
          $lexiconEntries = $this->lexiconEntryUtils->getLexiconTooltipsFromContent($body);
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

      if ($elearningExerciseId) {
        if (!$exerciseTitle) {
          if ($elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId)) {
            $exerciseTitle = $elearningExercise->getName();
          }
        }
        $url = "$gElearningUrl/exercise/display_exercise.php?elearningExerciseId=$elearningExerciseId";
        if ($exerciseTitle) {
          $this->renderParagraphExerciseLink($pdfDocument, $exerciseTitle, $url);
        }
      }
    }
  }

}

?>
