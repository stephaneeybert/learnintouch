<?PHP

require_once("website.php");

$adminModuleUtils->checkAdminModule(MODULE_ELEARNING);

$mlText = $languageUtils->getMlText(__FILE__);

$warnings = array();

$formSubmitted = LibEnv::getEnvHttpPOST("formSubmitted");

if ($formSubmitted) {

  $elearningQuestionId = LibEnv::getEnvHttpPOST("elearningQuestionId");
  $question = LibEnv::getEnvHttpPOST("question");
  $currentLanguageCode = LibEnv::getEnvHttpPOST("currentLanguageCode");
  $explanation = LibEnv::getEnvHttpPOST("explanation");
  $elearningExercisePageId = LibEnv::getEnvHttpPOST("elearningExercisePageId");
  $hint = LibEnv::getEnvHttpPOST("hint");
  $points = LibEnv::getEnvHttpPOST("points");
  $answerNbWords = LibEnv::getEnvHttpPOST("answerNbWords");
  $trueFalseAnswers = LibEnv::getEnvHttpPOST("trueFalseAnswers");
  $trueFalseSolution = LibEnv::getEnvHttpPOST("trueFalseSolution");

  $elearningQuestionId = LibString::cleanString($elearningQuestionId);
  $question = LibString::stripLineBreaks(trim($question));
  $question = LibString::cleanHtmlString($question);
  $currentLanguageCode = LibString::cleanString($currentLanguageCode);
  $explanation = LibString::cleanString($explanation);
  $elearningExercisePageId = LibString::cleanString($elearningExercisePageId);
  $hint = LibString::stripLineBreaks(trim($hint));
  $hint = LibString::cleanHtmlString($hint);
  $points = LibString::cleanString($points);
  $answerNbWords = LibString::cleanString($answerNbWords);
  $trueFalseAnswers = LibString::cleanString($trueFalseAnswers);
  $trueFalseSolution = LibString::cleanString($trueFalseSolution);

  // The number of points must be greater or equal to one
  if ($points && $points < 1) {
    array_push($warnings, $mlText[1]);
  }

  if (!$points) {
    $points = 1;
  }

  // An exercise page is required
  if (!$elearningExercisePageId) {
    array_push($warnings, $mlText[23]);
  }

  // The number of words for a typed in text, must be greater or equal to one
  if ($answerNbWords && $answerNbWords < 1) {
    array_push($warnings, $mlText[18]);
  }

  if (count($warnings) == 0) {

    if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
      $elearningQuestion->setQuestion($question);
      $elearningQuestion->setExplanation($languageUtils->setTextForLanguage($elearningQuestion->getExplanation(), $currentLanguageCode, $explanation));
      $elearningQuestion->setElearningExercisePage($elearningExercisePageId);
      $elearningQuestion->setHint($hint);
      $elearningQuestion->setPoints($points);
      $elearningQuestion->setAnswerNbWords($answerNbWords);
      if ($elearningExercisePageId != $elearningQuestion->getElearningExercisePage()) {
        $listOrder = $elearningQuestionUtils->getNextListOrder($elearningExercisePageId);
        $elearningQuestion->setListOrder($listOrder);
      }
      $elearningQuestionUtils->update($elearningQuestion);

      // Set the question display in a collapsed state
      LibSession::putSessionValue(ELEARNING_SESSION_DISPLAY_QUESTION . $elearningQuestionId, ELEARNING_COLLAPSED);
    } else {
      $elearningQuestion = new ElearningQuestion();
      $elearningQuestion->setQuestion($question);
      $elearningQuestion->setExplanation($languageUtils->setTextForLanguage('', $currentLanguageCode, $explanation));
      $elearningQuestion->setElearningExercisePage($elearningExercisePageId);
      $elearningQuestion->setHint($hint);
      $elearningQuestion->setPoints($points);
      $elearningQuestion->setAnswerNbWords($answerNbWords);
      $listOrder = $elearningQuestionUtils->getNextListOrder($elearningExercisePageId);
      $elearningQuestion->setListOrder($listOrder);
      $elearningQuestionUtils->insert($elearningQuestion);
      $elearningQuestionId = $elearningQuestionUtils->getLastInsertId();

      // Set the exercise display in a collapsed state
      LibSession::putSessionValue(ELEARNING_SESSION_DISPLAY_EXERCISE_PAGE . $elearningExercisePageId, ELEARNING_COLLAPSED);
    }

    // Add two true/false answers
    if ($trueFalseAnswers && $elearningQuestionId) {

      $elearningAnswer = new ElearningAnswer();
      $elearningAnswer->setAnswer($mlText[16]);
      $elearningAnswer->setElearningQuestion($elearningQuestionId);
      $listOrder = $elearningAnswerUtils->getNextListOrder($elearningQuestionId);
      $elearningAnswer->setListOrder($listOrder);
      $elearningAnswerUtils->insert($elearningAnswer);
      $elearningAnswerId = $elearningAnswerUtils->getLastInsertId();
      if ($trueFalseSolution) {
        $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
      }

      $elearningAnswer = new ElearningAnswer();
      $elearningAnswer->setAnswer($mlText[17]);
      $elearningAnswer->setElearningQuestion($elearningQuestionId);
      $listOrder = $elearningAnswerUtils->getNextListOrder($elearningQuestionId);
      $elearningAnswer->setListOrder($listOrder);
      $elearningAnswerUtils->insert($elearningAnswer);
      $elearningAnswerId = $elearningAnswerUtils->getLastInsertId();
      if (!$trueFalseSolution) {
        $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/exercise/compose.php");
    printContent($str);
    return;

  }

} else {

  $elearningQuestionId = LibEnv::getEnvHttpGET("elearningQuestionId");
  $elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");

  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();

  $question = '';
  $explanation = '';
  $hint = '';
  $points = '';
  $answerNbWords = '';
  if ($elearningQuestionId) {
    if ($elearningQuestion = $elearningQuestionUtils->selectById($elearningQuestionId)) {
      $question = $elearningQuestion->getQuestion();
      $explanation = $languageUtils->getTextForLanguage($elearningQuestion->getExplanation(), $currentLanguageCode);
      $elearningExercisePageId = $elearningQuestion->getElearningExercisePage();
      $hint = $elearningQuestion->getHint();
      $points = $elearningQuestion->getPoints();
      $answerNbWords = $elearningQuestion->getAnswerNbWords();
    }
  }

}

$elearningExerciseId = '';
if ($elearningExercisePage = $elearningExercisePageUtils->selectById($elearningExercisePageId)) {
  $elearningExerciseId = $elearningExercisePage->getElearningExerciseId();
}

$elearningExercisePages = $elearningExercisePageUtils->selectByExerciseId($elearningExerciseId);
$elearningExercisePageList = Array();
foreach ($elearningExercisePages as $elearningExercisePage) {
  $wElearningExercisePageId = $elearningExercisePage->getId();
  $wName = $elearningExercisePage->getName();
  $elearningExercisePageList[$wElearningExercisePageId] = $wName;
}
$strSelectElearningExercisePage = LibHtml::getSelectList("elearningExercisePageId", $elearningExercisePageList, $elearningExercisePageId);

$strWarning = '';
if (count($warnings) > 0) {
  foreach ($warnings as $warning) {
    $strWarning .= "<br>$warning";
  }
}

$panelUtils->setHeader($mlText[0], "$gElearningUrl/exercise/compose.php");
$panelUtils->addLine($panelUtils->addCell($strWarning, "wb"));
$help = $popupUtils->getHelpPopup($mlText[6], 300, 500);
$panelUtils->setHelp($help);
$panelUtils->openForm($PHP_SELF);
$label = $popupUtils->getTipPopup($mlText[2], $mlText[7], 300, 200);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), $strSelectElearningExercisePage);
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
$strLexiconQuestion = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageLexicon' title='$mlText[21]'>", "$gLexiconUrl/lexicon.php?elementId=question", 600, 600);
$strLexiconQuestionClear = "<a href=\"javascript:lexiconClear('question');\" $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageLexiconClear' title='$mlText[22]'></a>";
$panelUtils->addLine($panelUtils->addCell($mlText[4], "nbr"), "<textarea id='question' name='question' cols='30' rows='3'>\n$question\n</textarea> $strLexiconQuestion $strLexiconQuestionClear");
$panelUtils->addLine();
$strLexiconHint = $popupUtils->getDialogPopup("<img border='0' src='$gCommonImagesUrl/$gImageLexicon' title='$mlText[21]'>", "$gLexiconUrl/lexicon.php?elementId=hint", 600, 600);
$strLexiconHintClear = "<a href=\"javascript:lexiconClear('hint');\" $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageLexiconClear' title='$mlText[22]'></a>";
$label = $popupUtils->getTipPopup($mlText[8], $mlText[9], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<textarea id='hint' name='hint' cols='30' rows='3'>\n$hint\n</textarea> $strLexiconHint $strLexiconHintClear");
$panelUtils->addLine();
// The explanation could not be saved on a newly created question
if ($elearningQuestionId) {
  $label = $popupUtils->getTipPopup($mlText[10], $mlText[11], 300, 500);
  $strEditor = "<textarea id='explanation' name='explanation' cols='30' rows='5'>$explanation</textarea>";
  $strJsChangeWebsiteLanguage = <<<HEREDOC
<script type='text/javascript'>
function changeWebsiteLanguage(languageCode) {
  var url = '$gElearningUrl/question/getExplanation.php?elearningQuestionId=$elearningQuestionId&languageCode='+languageCode;
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
  var params = {"elearningQuestionId" : "$elearningQuestionId", "languageCode" : languageCode, "explanation" : explanation};
  ajaxAsynchronousPOSTRequest("$gElearningUrl/question/update.php", params);
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
$label = $popupUtils->getTipPopup($mlText[3], $mlText[5], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='points' value='$points' size='3'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[19], $mlText[20], 300, 300);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='text' name='answerNbWords' value='$answerNbWords' size='3'>");
$panelUtils->addLine();
$label = $popupUtils->getTipPopup($mlText[12], $mlText[13], 300, 300);
$trueFalseList = array(true => $mlText[16], false => $mlText[17]);
$strSelectTrueFalse = LibHtml::getSelectList("trueFalseSolution", $trueFalseList, true);
$panelUtils->addLine($panelUtils->addCell($label, "nbr"), "<input type='checkbox' name='trueFalseAnswers' value='1'> $mlText[14] $strSelectTrueFalse");
$panelUtils->addLine();
$panelUtils->addLine('', $panelUtils->getOk());
$panelUtils->addHiddenField('formSubmitted', 1);
$panelUtils->addHiddenField('elearningQuestionId', $elearningQuestionId);
$panelUtils->closeForm();
$str = $panelUtils->render();

printAdminPage($str);

?>
