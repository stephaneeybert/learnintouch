<?PHP

require_once("website.php");

LibHtml::preventCaching();

$textareaId = LibEnv::getEnvHttpGET("textareaId");
$renderNbWordsId = LibEnv::getEnvHttpGET("renderNbWordsId");
$progressBarId = LibEnv::getEnvHttpGET("progressBarId");
$str = LibEnv::getEnvHttpGET("str");
$answerNbWords = LibEnv::getEnvHttpGET("answerNbWords");

// An ajax request parameter value is UTF-8 encoded
$textareaId = utf8_decode($textareaId);
$renderNbWordsId = utf8_decode($renderNbWordsId);
$progressBarId = utf8_decode($progressBarId);
$str = utf8_decode($str);
$answerNbWords = utf8_decode($answerNbWords);

// Remove backslashes before quotes if any
// The backslashes must be removed only if the sent value is coming from an ajax request
// When the sent value is coming from a regular http post request the backslashes are not present
$str = LibString::stripBSlashes($str);

$nbWords = LibString::countNbRealWords($str);

$responseText = <<<HEREDOC
{
  "textareaId" : "$textareaId",
  "renderNbWordsId" : "$renderNbWordsId",
  "progressBarId" : "$progressBarId",
  "nbWords" : "$nbWords",
  "answerNbWords" : "$answerNbWords"
}
HEREDOC;

print($responseText);

?>
