<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningAnswerId = LibEnv::getEnvHttpPOST("elearningAnswerId");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $answer = LibEnv::getEnvHttpPOST("answer");
  $explanation = LibEnv::getEnvHttpPOST("explanation");
  $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");

  $elearningAnswerId = LibString::cleanString($elearningAnswerId);
  $answer = LibString::stripLineBreaks(trim($answer));
  $answer = LibString::cleanHtmlString($answer);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $explanation = LibString::cleanString($explanation);
  $elearningQuestionId = LibString::cleanString($elearningQuestionId);

  if (count($warnings) == 0) {

  if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
    $elearningAnswer->setAnswer($answer);
    $elearningAnswer->setExplanation($languageUtils->setTextForLanguage($elearningAnswer->getExplanation(), $currentLanguageCode, $explanation));
    $elearningAnswer->setElearningQuestion($elearningQuestionId);
    if ($elearningQuestionId != $elearningAnswer->getElearningQuestion()) {
      $listOrder = $elearningAnswerUtils->getNextListOrder($elearningQuestionId);
      $elearningAnswer->setListOrder($listOrder);
      }
    $elearningAnswerUtils->update($elearningAnswer);
    } else {
    $elearningAnswer = new ElearningAnswer();
    $elearningAnswer->setAnswer($answer);
    $elearningAnswer->setExplanation($languageUtils->setTextForLanguage('', $currentLanguageCode, $explanation));
    $elearningAnswer->setElearningQuestion($elearningQuestionId);
    $listOrder = $elearningAnswerUtils->getNextListOrder($elearningQuestionId);
    $elearningAnswer->setListOrder($listOrder);
    $elearningAnswerUtils->insert($elearningAnswer);
    $elearningAnswerId = $elearningAnswerUtils->getLastInsertId();

    // If the answer is the first and only one then set it as the solution
    if ($elearningAnswers = $elearningAnswerUtils->selectByQuestion($elearningQuestionId)) {
      if (count($elearningAnswers) == 1) {
        $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
        }
      }

    // Set the question display in a collapsed state
    LibSession::putSessionValue(ELEARNING_SESSION_DISPLAY_QUESTION . $elearningQuestionId, ELEARNING_COLLAPSED);
    }

  $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
  printMessage($str);
  return;

  }

  } else {

  $elearningAnswerId = LibEnv::getEnvHttpGET("elearningAnswerId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $answer = '';
  $explanation = '';
  if ($elearningAnswerId) {
    if ($elearningAnswer = $elearningAnswerUtils->selectById($elearningAnswerId)) {
      $answer = $elearningAnswer->getAnswer();
      $explanation = $languageUtils->getTextForLanguage($elearningAnswer->getExplanation(), $currentLanguageCode);
      $elearningQuestionId = $elearningAnswer->getElearningQuestion();
      }
    }

  }

  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
  if (!$elearningExercisePageId) {
    $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
    }
  $elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
  if (!$elearningQuestionId) {
    $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
    }

  $elearningQuestions = $elearningQuestionUtils->selectByExercisePage($elearningExercisePageId);
  $elearningQuestionList = Array();
  foreach ($elearningQuestions as $elearningQuestion) {
    $wElearningQuestionId = $elearningQuestion->getId();
    $wQuestion = $elearningQuestion->getQuestion();
    $elearningQuestionList[$wElearningQuestionId] = $wQuestion;
    }
  $strSelectElearningQuestion = LibHtml::getSelectList("elearningQuestionId", $elearningQuestionList, $elearningQuestionId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
    }
  }

  $panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
  $panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
  $panelUtils->openForm($PHP_SELF);
  $panelUtils->addLine($panelUtils->addCell($mlText[2], "nbr"), $strSelectElearningQuestion);
  $panelUtils->addLine();
  $strJsLexiconClear = <<<HEREDOC
<script type='text/javascript'>
function lexiconClear(elementId) {
  var element = document.getElementById(elementId);
  document.getElementById(elementId).value = stripTags(element.value);
}
</script>
HEREDOC;
  $panelUtils->addContent($strJsLexiconClear);
  $strLexiconAnswer = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageLexicon' title='$mlText[5]'>", "$gLexiconUrl/lexicon.php?elementId=answer", 600, 600);
  $strLexiconAnswerClear = "<a href=\"javascript:lexiconClear('answer');\" $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageLexiconClear' title='$mlText[6]'></a>";
  $panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<textarea id='answer' name='answer' cols='30' rows='3'>\n$answer\n</textarea> $strLexiconAnswer $strLexiconAnswerClear");
  $panelUtils->addLine();
  // The explanation could not be saved on a newly created answer
  if ($elearningAnswerId) {
    $label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 500);
    $strEditor = "<textarea id='explanation' name='explanation' cols='30' rows='5'>$explanation</textarea>";
    $strJsChangeWebsiteLanguage = <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gElearningUrl/answer/get_explanation.php?elearningAnswerId=$elearningAnswerId&languageCode='+languageCode;
  document.getElementById('currentLanguageCode').value = languageCode;
  ajaxAsynchronousRequest(url, updateExplanation);
}
function updateExplanation(responseText) {
  var response = eval('(' + responseText + ')');
  var explanation = response.explanation;
  document.getElementById('explanation').value = explanation;
}
function saveExplanation() {
  var explanation = document.getElementById('explanation').value;
  explanation = encodeURIComponent(explanation);
  var languageCode = document.getElementById('currentLanguageCode').value;
  var params = {"elearningAnswerId" : "$elearningAnswerId", "languageCode" : languageCode, "explanation" : explanation};
  ajaxAsynchronousPOSTRequest("$gElearningUrl/answer/update_explanation.php", params);
}
</script>
HEREDOC;
    $panelUtils->addContent($strJsChangeWebsiteLanguage);
    $panelUtils->addHiddenField('currentLanguageCode', $currentLanguageCode);
    $strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
    $strSave = "<a href='javascript:saveExplanation();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageFloppy' title='$mlText[15]' style='margin-top:2px;'></a>";
    $panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strEditor . '<br/>' . $strLanguageFlag . ' ' . $strSave);
    $panelUtils->addLine();
  }
  $panelUtils->addLine('', $panelUtils->getOk());
  $panelUtils->addHiddenField('formSubmitted', 1);
  $panelUtils->addHiddenField('elearningAnswerId', $elearningAnswerId);
  $panelUtils->closeForm();
  $str = $panelUtils->render();

  printAdminPage($str);

?>
