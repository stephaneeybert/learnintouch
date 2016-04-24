<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningExercisePageId = LibEnv::getEnvHttpGET("elearningExercisePageId");
$languageCode = LibEnv::getEnvHttpGET("languageCode");
$questionType = LibEnv::getEnvHttpGET("questionType");

$websiteText = $languageUtils->getText(__FILE__, $languageCode);

if ($questionType == 'SELECT_LIST' || $questionType == 'RADIO_BUTTON_LIST_H' || $questionType == 'RADIO_BUTTON_LIST_V') {
  $instructions = $websiteText[12];
} else if ($questionType == 'SOME_CHECKBOXES') {
  $instructions = $websiteText[13];
} else if ($questionType == 'ALL_CHECKBOXES') {
  $instructions = $websiteText[14];
} else if ($questionType == 'WRITE_IN_QUESTION') {
  $instructions = $websiteText[15];
} else if ($questionType == 'SELECT_LIST_IN_TEXT') {
  $instructions = $websiteText[16];
} else if ($questionType == 'WRITE_IN_TEXT') {
  $instructions = $websiteText[17];
} else if ($questionType == 'WRITE_TEXT') {
  $instructions = $websiteText[11];
} else if ($questionType == 'DRAG_ANSWER_IN_QUESTION') {
  $instructions = $websiteText[18];
} else if ($questionType == 'DRAG_ANSWER_IN_ANY_QUESTION') {
  $instructions = $websiteText[19];
} else if ($questionType == 'DRAG_ORDER_SENTENCE') {
  $instructions = $websiteText[20];
} else if ($questionType == 'DRAG_ANSWER_IN_TEXT_HOLE') {
  $instructions = $websiteText[21];
} else if ($questionType == 'DRAG_ANSWERS_UNDER_ANY_QUESTION') {
  $instructions = $websiteText[22];
} else {
  $instructions = $websiteText[12];
}

$instructions = LibString::jsonEscapeLinebreak($instructions);
$instructions = LibString::escapeDoubleQuotes($instructions);

$responseText = <<<HEREDOC
{
"instructions" : "$instructions"
}
HEREDOC;

print($responseText);

?>
