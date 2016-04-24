<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningCourseInfoId = LibEnv::getEnvHttpGET("elearningCourseInfoId");
$targetId = LibEnv::getEnvHttpGET("targetId");

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
