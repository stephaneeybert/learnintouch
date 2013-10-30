<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningCourseInfoId = LibEnv::getEnvHttpGET("elearningCourseInfoId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$elearningCourseInfoId = utf8_decode($elearningCourseInfoId);
$targetId = utf8_decode($targetId);

$moved = false;

if ($elearningCourseInfo = $elearningCourseInfoUtils->selectById($elearningCourseInfoId)) {
  $moved = $elearningCourseInfoUtils->placeAfter($elearningCourseInfoId, $targetId);
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
