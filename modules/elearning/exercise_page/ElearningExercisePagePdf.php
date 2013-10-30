<?

class ElearningExercisePagePdf extends PdfUtils {

  var $mlText;

  var $languageUtils;
  var $adminUtils;
  var $lexiconEntryUtils;
  var $elearningExerciseUtils;
  var $elearningExercisePageUtils;
  var $elearningQuestionUtils;
  var $elearningAnswerUtils;

  function ElearningExercisePagePdf() {
  }

  function loadLanguageTexts() {
    if ($this->adminUtils->getLoggedAdminId()) {
      $this->mlText = $this->languageUtils->getMlText(__FILE__, false);
    } else {
      $this->mlText = $this->languageUtils->getWebsiteText(__FILE__, false);
    }
  }

  function renderExerciseName(&$pdfDocument, $name) {
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

  function renderExercisePageImage(&$pdfDocument, $image) {
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

  function renderText(&$pdfDocument, $text) {
    $pdfDocument->Ln();
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(0);
    $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $text, 0, 'L');
  }

  function render(&$pdfDocument, $elearningExercisePage) {
    $this->loadLanguageTexts();

    $name = $elearningExercisePage->getName();
    $description = $elearningExercisePage->getDescription();
    $image = $elearningExercisePage->getImage();

    $name = $this->pdfCleanString($name);
    $description = $this->pdfCleanString($description);

    $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
    $elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId);
    $exerciseName = $elearningExercise->getName();
    $exerciseName = $this->pdfCleanString($exerciseName);
    if (strlen($exerciseName) > 40) {
      $exerciseName = substr($exerciseName, 0, 40) . '...';
    }
    $this->renderExerciseName($pdfDocument, $this->mlText[1]);

    $this->renderName($pdfDocument, $name);

    if ($description) {
      $this->renderDescription($pdfDocument, $description);
    }

    $imagePath = $this->elearningExercisePageUtils->imageFilePath;
    if ($image && is_file($imagePath . $image)) {
      $filename = LibImage::getJpgImage($imagePath, $image);
      if ($filename) {
        $this->renderExercisePageImage($pdfDocument, $filename);
      }
    }

    $instructions = $this->elearningExercisePageUtils->getStartInstructions($elearningExercisePage);
    $instructions = $this->pdfCleanString($instructions);
    if ($instructions) {
      $this->renderInstructions($pdfDocument, $instructions);
    }

    $text = $elearningExercisePage->getText();
    if ($text) {
      $text = preg_replace('/ELEARNING_ANSWER_MCQ_MARKER([0-9]*)/', ELEARNING_ANSWER_UNDERSCORE, $text);
      $cleanedUpText = $this->pdfCleanString($text);
      $this->renderText($pdfDocument, $cleanedUpText);

      if ($this->elearningExerciseUtils->displayLexiconList()) {
        $lexiconEntries = $this->lexiconEntryUtils->getLexiconTooltipsFromContent($text);
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
    $pdfDocument->Ln();
    $pdfDocument->Ln();

    $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());

    if ($this->elearningExercisePageUtils->typeIsDragAndDropOneAnswerInAnyQuestion($elearningExercisePage) || $this->elearningExercisePageUtils->typeIsDragAndDropSeveralAnswersUnderAnyQuestion($elearningExercisePage) || $this->elearningExercisePageUtils->typeIsDragAndDropInText($elearningExercisePage)) {

      // Shuffle the answers across all questions
      $shuffledAnswers = array();
      $allQuestionsAnswers = array();
      foreach ($elearningQuestions as $elearningQuestion) {
        $elearningQuestionId = $elearningQuestion->getId();
        $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
        foreach ($elearningAnswers as $elearningAnswer) {
          array_push($allQuestionsAnswers, $elearningAnswer);
        }
      }
      $allQuestionsAnswers = LibUtils::shuffleArray($allQuestionsAnswers);

      // Shuffle the questions
      if ($this->elearningExercisePageUtils->shuffleQuestions()) {
        $elearningQuestions = LibUtils::shuffleArray($elearningQuestions);
      }

      $questionCellWidth = (PDF_A4_PAGE_WIDTH - $pdfDocument->GetX()) * 70 / 100;
      $answerCellWidth = (PDF_A4_PAGE_WIDTH - $pdfDocument->GetX()) - $questionCellWidth;
      $pdfDocument->SetFont('Arial', '', 12);
      $pdfDocument->SetTextColor(0);
      $pdfDocument->SetAligns(array('L', 'R'));
      $xBeforeQuestions = $pdfDocument->GetX();
      $yBeforeQuestions = $pdfDocument->GetY();
      foreach ($elearningQuestions as $elearningQuestion) {
        $question = $this->getQuestionSentence($pdfDocument, $elearningQuestion, false);
        $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
        if (count($questionBits) > 1) {
          $question = $questionBits[0] . ' ';
          $question .= ELEARNING_ANSWER_UNDERSCORE;
          $question .= ' ' . $questionBits[1];
        } else {
          $question .= ' ' . ELEARNING_ANSWER_UNDERSCORE;
        }
        $this->renderQuestionImage($pdfDocument, $elearningQuestion);
        $pdfDocument->MultiCell($questionCellWidth, $pdfDocument->lineHeight, $question, 0, 'L');
        $pdfDocument->Ln();
      }
      $xAfterQuestions = $pdfDocument->GetX();
      $yAfterQuestions = $pdfDocument->GetY();

      $pdfDocument->SetXY($xBeforeQuestions, $yBeforeQuestions);
      foreach ($allQuestionsAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        $answer = $elearningAnswer->getAnswer();
        $answer = $this->pdfCleanString($answer);
        if ($answer) {
          $answer = "[$answer]";
        }
        $pdfDocument->SetX($questionCellWidth);
        $pdfDocument->MultiCell($answerCellWidth, $pdfDocument->lineHeight, $answer, 0, 'R');
        $image = $elearningAnswer->getImage();
        $imagePath = $this->elearningAnswerUtils->imageFilePath;
        if ($image && is_file($imagePath . $image)) {
          $filename = LibImage::getJpgImage($imagePath, $image);
          if ($filename) {
            $imageWidth = $this->getImageWidthInMm(LibImage::getWidth($filename));
            $x = PDF_A4_PAGE_WIDTH - $pdfDocument->GetX() - $imageWidth;
            $y = $pdfDocument->GetY();
            $imageHeight = $this->getImageHeightInMm(LibImage::getHeight($filename));
            $lineBreak = $this->lineBreakIfImageIsTooBig($pdfDocument, $y, $imageHeight);
            if ($lineBreak) {
              $pdfDocument->AddPage();
            }
            $pdfDocument->Image($filename, $x, $y, '', '', '', '');
            $pdfDocument->Ln($imageHeight);
            $pdfDocument->Ln();
          }
        }
      }
      $pdfDocument->SetXY($xAfterQuestions, $yAfterQuestions);
    } else if ($this->elearningExercisePageUtils->typeIsDragAndDropOrderSentence($elearningExercisePage)) {
      $this->renderAnswers($pdfDocument, $elearningExercisePage, false, true);
    } else if (!$this->elearningExercisePageUtils->typeIsWriteInText($elearningExercisePage)) {
      $this->renderAnswers($pdfDocument, $elearningExercisePage, false, false);
    }
  }

  function renderSolutionsPage(&$pdfDocument, $elearningExercisePage) {
    $name = $elearningExercisePage->getName();
    $description = $elearningExercisePage->getDescription();

    $name = $this->pdfCleanString($name);
    $description = $this->pdfCleanString($description);

    $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
    $elearningExercise = $this->elearningExerciseUtils->selectById($elearningExerciseId);
    $exerciseName = $elearningExercise->getName();
    $exerciseName = $this->pdfCleanString($exerciseName);
    if (strlen($exerciseName) > 40) {
      $exerciseName = substr($exerciseName, 0, 40) . '...';
    }
    $this->renderExerciseName($pdfDocument, $this->mlText[2]);

    $this->renderName($pdfDocument, $name);

    if ($description) {
      $this->renderDescription($pdfDocument, $description);
    }

    $pdfDocument->Ln();
    $this->renderAnswers($pdfDocument, $elearningExercisePage, true, false);
  }

  function renderQuestions(&$pdfDocument, $elearningExercisePage, $showSolutions) {
    $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());
    foreach ($elearningQuestions as $elearningQuestion) {
      $elearningQuestionId = $elearningQuestion->getId();
      $this->renderQuestionImage($pdfDocument, $elearningQuestion);

      $question = $this->getQuestionSentence($pdfDocument, $elearningQuestion, $showSolutions);

      $pdfDocument->SetFont('Arial', '', 12);
      $pdfDocument->SetTextColor(0);
      $pdfDocument->Ln();
      $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $question, 0, 'L');
    }
  }

  function renderQuestionImage(&$pdfDocument, $elearningQuestion) {
    $image = $elearningQuestion->getImage();
    $imagePath = $this->elearningQuestionUtils->imageFilePath;
    if ($image && is_file($imagePath . $image)) {
      $filename = LibImage::getJpgImage($imagePath, $image);
      if ($filename) {
        $y = $pdfDocument->GetY();
        $x = $pdfDocument->GetX();
        $imageHeight = $this->getImageHeightInMm(LibImage::getHeight($filename));
        $lineBreak = $this->lineBreakIfImageIsTooBig($pdfDocument, $y, $imageHeight);
        if ($lineBreak) {
          $pdfDocument->AddPage();
        }
        $pdfDocument->Image($filename, $x, $y, '', '', '', '');
        $pdfDocument->Ln($imageHeight);
      }
    }
  }

  function getQuestionSentence(&$pdfDocument, $elearningQuestion, $showSolutions) {
    $question = $elearningQuestion->getQuestion();
    $question = $this->pdfCleanString($question);

    if (!$question) {
      $question = '-';
    }

    if (!$showSolutions) {
      $hint = $elearningQuestion->getHint();
      if ($hint) {
        $strHint = "($hint)";
        if ($this->elearningQuestionUtils->hintBeforeAnswer($elearningQuestion)) {
          $question = str_replace(ELEARNING_ANSWER_MCQ_MARKER, $strHint . ' ' . ELEARNING_ANSWER_MCQ_MARKER, $question);
        } else if ($this->elearningQuestionUtils->hintAfterAnswer($elearningQuestion) || $this->elearningQuestionUtils->hintInPopup($elearningQuestion) || $this->elearningQuestionUtils->hintInsideAnswer($elearningQuestion)) {
          $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
          $inputSize = $this->elearningExercisePageUtils->getQuestionInputFieldSize($question);
          $question = $questionBits[0] . ' ' . ELEARNING_ANSWER_MCQ_MARKER . $inputSize . ' ' . $strHint;
          if (count($questionBits) > 1) {
            $question .= ' ' . $questionBits[1];
          }
        } else {
          $question .= ' ' . $strHint;
        }
      }
    }

    $question = $this->pdfCleanString($question);

    return($question);
  }

  function renderAnswers(&$pdfDocument, $elearningExercisePage, $showSolutions, $shuffleAnswers) {
    $pdfDocument->SetFont('Arial', '', 12);
    $pdfDocument->SetTextColor(0);

    $elearningQuestions = $this->elearningQuestionUtils->selectByExercisePage($elearningExercisePage->getId());
    foreach ($elearningQuestions as $elearningQuestion) {
      $this->renderQuestionAnswers($pdfDocument, $elearningQuestion, $showSolutions, $shuffleAnswers);
    }
  }

  function renderQuestionAnswers(&$pdfDocument, $elearningQuestion, $showSolutions, $shuffleAnswers) {
    $question = $this->getQuestionSentence($pdfDocument, $elearningQuestion, $showSolutions);
    $elearningQuestionId = $elearningQuestion->getId();
    $elearningAnswers = $this->elearningAnswerUtils->selectByQuestion($elearningQuestionId);
    if ($shuffleAnswers) {
      $elearningAnswers = LibUtils::shuffleArray($elearningAnswers);
    }

    // Underscores are displayed for the questions of type written answer when displaying the questions without solutions, all answers are displayed for the questions of type other than written answer when displaying the questions without solutions, and only the solutions (and not all answers) are displayed when displaying the questions with their solutions
    if (!$showSolutions) {
      if ($this->elearningQuestionUtils->isWrittenAnswer($elearningQuestion)) {
        $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
        if (count($questionBits) > 1) {
          $question = $questionBits[0] . ' ';
          $question .= ELEARNING_ANSWER_UNDERSCORE;
          $question .= ' ' . $questionBits[1];
        } else {
          $question .= ' ' . ELEARNING_ANSWER_UNDERSCORE;
        }
        $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $question, 0, 'L');
      } else {
        $answers = '';
        foreach ($elearningAnswers as $elearningAnswer) {
          $elearningAnswerId = $elearningAnswer->getId();
          $answer = $elearningAnswer->getAnswer();
          $answer = $this->pdfCleanString($answer);
          $answers .= ' [' . $answer . ']';
        }
        $answers = trim($answers);
        if ($this->elearningQuestionUtils->typeIsDragAndDropOrderSentence($elearningQuestion)) {
          $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $answers, 0, 'L');
        } else {
          $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
          if (count($questionBits) > 1) {
            $question = $questionBits[0] . ' ' . $answers . ' ' . $questionBits[1];
            $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $question, 0, 'L');
          } else {
            $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $question, 0, 'L');
            $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $answers, 0, 'L');
          }
          foreach ($elearningAnswers as $elearningAnswer) {
            $this->renderAnswerImage($pdfDocument, $elearningAnswer);
          }
        }
      }
    } else {
      $answers = '';
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $elearningAnswerId)) {
          $answer = $elearningAnswer->getAnswer();
          $answer = $this->pdfCleanString($answer);
          $answers .= ' [' . $answer . ']';
        }
      }
      $answers = trim($answers);
      if (!$this->elearningQuestionUtils->typeIsDragAndDropOrderSentence($elearningQuestion)) {
        $questionBits = $this->elearningExercisePageUtils->getQuestionBits($question);
        if (count($questionBits) > 1) {
          $question = $questionBits[0] . ' ' . $answers . ' ' . $questionBits[1];
        } else {
          $question .= ' ' . $answers;
        }
      }
      $pdfDocument->MultiCell(0, $pdfDocument->lineHeight, $question, 0, 'L');
      foreach ($elearningAnswers as $elearningAnswer) {
        $elearningAnswerId = $elearningAnswer->getId();
        if ($this->elearningAnswerUtils->isASolution($elearningQuestion, $elearningAnswerId)) {
          $this->renderAnswerImage($pdfDocument, $elearningAnswer);
        }
      }
    }
  }

  function renderAnswerImage(&$pdfDocument, $elearningAnswer) {
    $image = $elearningAnswer->getImage();
    $imagePath = $this->elearningAnswerUtils->imageFilePath;
    if ($image && is_file($imagePath . $image)) {
      $filename = LibImage::getJpgImage($imagePath, $image);
      if ($filename) {
        $x = $pdfDocument->GetX();
        $y = $pdfDocument->GetY();
        $imageWidth = $this->getImageWidthInMm(LibImage::getWidth($filename));
        $imageHeight = $this->getImageHeightInMm(LibImage::getHeight($filename));
        $lineBreak = $this->lineBreakIfImageIsTooBig($pdfDocument, $y, $imageHeight);
        if ($lineBreak) {
          $pdfDocument->AddPage();
        }
        $pdfDocument->Image($filename, $x, $y, '', '', '', '');
        $pdfDocument->Ln($imageHeight);
        $pdfDocument->Ln();
        //        $pdfDocument->Ln($imageHeight / 2 - $pdfDocument->lineHeight / 2);
        //        $pdfDocument->SetX($x + $imageWidth + 4);
      }
      //      $pdfDocument->SetY($y);
    }
  }

}

?>
