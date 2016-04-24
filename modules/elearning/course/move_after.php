<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningCourseItemId = LibEnv::getEnvHttpGET("elearningCourseItemId");
$targetId = LibEnv::getEnvHttpGET("targetId");

$moved = false;

if ($elearningCourseItem = $elearningCourseItemUtils->selectById($elearningCourseItemId)) {
  $moved = $elearningCourseItemUtils->placeAfter($elearningCourseItemId, $targetId);
}

$responseText = <<<HEREDOC
{
"moved" : "$moved"
}
HEREDOC;

print($responseText);

?>
