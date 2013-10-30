<?PHP

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

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
    array_push($warnings, $websiteText[1]);
  }

  if (!$points) {
    $points = 1;
  }

  // The number of words for a typed in text, must be greater or equal to one
  if ($answerNbWords && $answerNbWords < 1) {
    array_push($warnings, $websiteText[18]);
  }

  // The content must belong to the user
  if ($elearningQuestionId && !$elearningQuestionUtils->createdByUser($elearningQuestionId, $userId)) {
    array_push($warnings, $websiteText[23]);
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
      $elearningAnswer->setAnswer($websiteText[16]);
      $elearningAnswer->setElearningQuestion($elearningQuestionId);
      $listOrder = $elearningAnswerUtils->getNextListOrder($elearningQuestionId);
      $elearningAnswer->setListOrder($listOrder);
      $elearningAnswerUtils->insert($elearningAnswer);
      $elearningAnswerId = $elearningAnswerUtils->getLastInsertId();
      if ($trueFalseSolution) {
        $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
      }

      $elearningAnswer = new ElearningAnswer();
      $elearningAnswer->setAnswer($websiteText[17]);
      $elearningAnswer->setElearningQuestion($elearningQuestionId);
      $listOrder = $elearningAnswerUtils->getNextListOrder($elearningQuestionId);
      $elearningAnswer->setListOrder($listOrder);
      $elearningAnswerUtils->insert($elearningAnswer);
      $elearningAnswerId = $elearningAnswerUtils->getLastInsertId();
      if (!$trueFalseSolution) {
        $elearningAnswerUtils->specifyAsSolution($elearningAnswerId);
      }
    }

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
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

// Get the exercise id
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

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$help = $popupUtils->getTipPopup("<img src='$gImagesUserUrl/" . IMAGE_COMMON_QUESTION_MARK_MEDIUM . "' class='no_style_image_icon' title='' alt='' />", $websiteText[6], 300, 500);

$str .= "\n<div style='text-align:right;'>$help</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/question/edit.php' method='post'>";

$label = $popupUtils->getTipPopup($websiteText[2], $websiteText[7], 300, 200);
$str .= "<div class='system_label'>$label</div>"
  . "<div class='system_field'>$strSelectElearningExercisePage</div>";

$str .= "<div class='system_label'>$websiteText[4]</div>"
  . "<div class='system_field'><input type='text' name='question' value='$question' size='30'></div>";

$label = $popupUtils->getTipPopup($websiteText[8], $websiteText[9], 300, 300);
$str .= "<div class='system_label'>$label</div>"
  . "<div class='system_field'><input type='text' name='hint' value='$hint' size='30' maxlength='255'></div>";

// The explanation could not be saved on a newly created question
if ($elearningQuestionId) {
  $label = $popupUtils->getTipPopup($websiteText[10], $websiteText[11], 300, 500);
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
  $str .= $strJsChangeWebsiteLanguage;
  $str .= "\n<input type='hidden' name='currentLanguageCode' id='currentLanguageCode' value='$currentLanguageCode' />";
  $strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
  $strSave = "<a href='javascript:saveExplanation();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageFloppy' title='$websiteText[15]' style='margin-top:2px;'></a>";
  $str .= "<div class='system_label'>$label</div>"
    . "<div class='system_field'>$strEditor<br/>$strLanguageFlag $strSave</div>";
}
$label = $popupUtils->getTipPopup($websiteText[3], $websiteText[5], 300, 300);
$str .= "<div class='system_label'>$label</div>"
  . "<div class='system_field'><input type='text' name='points' value='$points' size='3'></div>";

$label = $popupUtils->getTipPopup($websiteText[19], $websiteText[20], 300, 300);
$str .= "<div class='system_label'>$label</div>"
  . "<div class='system_field'><input type='text' name='answerNbWords' value='$answerNbWords' size='3'></div>";

$label = $popupUtils->getTipPopup($websiteText[12], $websiteText[13], 300, 300);
$trueFalseList = array(true => $websiteText[16], false => $websiteText[17]);
$strSelectTrueFalse = LibHtml::getSelectList("trueFalseSolution", $trueFalseList, true);
$str .= "<div class='system_label'>$label</div>"
  . "<div class='system_field'><input type='checkbox' name='trueFalseAnswers' value='1'> $websiteText[14] $strSelectTrueFalse</div>";

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[21]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningQuestionId' value='$elearningQuestionId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[22]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
