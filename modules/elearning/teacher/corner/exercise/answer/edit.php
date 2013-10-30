<?PHP

require_once("website.php");


$websiteText = $languageUtils->getWebsiteText(__FILE__);

$elearningExerciseUtils->checkUserLogin();
$userId = $userUtils->getLoggedUserId();

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

  // The content must belong to the user
  if ($elearningAnswerId && !$elearningAnswerUtils->createdByUser($elearningAnswerId, $userId)) {
    array_push($warnings, $websiteText[9]);
  }

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

    $str = LibHtml::urlRedirect("$gElearningUrl/teacher/corner/exercise/compose.php");
    printContent($str);
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

$str = '';

$str .= "\n<div class='system'>";

$str .= "\n<div class='system_title'>$websiteText[0]</div>";

$str .= $commonUtils->renderWarningMessages($warnings);

$str .= "\n<form name='edit' id='edit' action='$gElearningUrl/teacher/corner/exercise/answer/edit.php' method='post'>";

$str .= "<div class='system_label'>$websiteText[2]</div>"
  . "<div class='system_field'>$strSelectElearningQuestion</div>";

$str .= "<div class='system_label'>$websiteText[4]</div>"
  . "<div class='system_field'><input type='text' name='answer' value='$answer' size='30'></div>";

// The explanation could not be saved on a newly created answer
if ($elearningAnswerId) {
  $label = $popupUtils->getTipPopup($websiteText[10], $websiteText[11], 300, 500);
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
  $str .= $strJsChangeWebsiteLanguage;
  $str .= "\n<input type='hidden' name='currentLanguageCode' id='currentLanguageCode' value='$currentLanguageCode' />";
  $strLanguageFlag = $languageUtils->renderChangeWebsiteLanguageBar($currentLanguageCode);
  $strSave = "<a href='javascript:saveExplanation();' $gJSNoStatus><img border='0' src='$gCommonImagesUrl/$gImageFloppy' title='$websiteText[15]' style='margin-top:2px;'></a>";
  $str .= "<div class='system_label'>$label</div>"
    . "<div class='system_field'>$strEditor<br/>$strLanguageFlag $strSave</div>";
}

$str .= "\n<div class='system_okay_button'><input type='image' src='$gImagesUserUrl/" . IMAGE_COMMON_OKAY . "' style='vertical-align:middle;' /> <a href='#' onclick=\"document.forms['edit'].submit(); return false;\" style='text-decoration:none; vertical-align:middle;'>$websiteText[21]</a></div>";

$str .= "\n<input type='hidden' name='formSubmitted' value='1' />";
$str .= "\n<input type='hidden' name='elearningAnswerId' value='$elearningAnswerId' />";

$str .= "\n</form>";

$str .= "\n<div class='system_cancel_button'><a href='$gElearningUrl/teacher/corner/exercise/compose.php' style='text-decoration:none; vertical-align:middle;'><img src='$gImagesUserUrl/" . IMAGE_COMMON_CANCEL . "' style='vertical-align:middle;' /> $websiteText[22]</a></div>";

$str .= "\n</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
