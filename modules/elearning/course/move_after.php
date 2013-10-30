<?PHP

require_once("website.php");

LibHtml::preventCaching();

$elearningCourseItemId = LibEnv::getEnvHttpGET("elearningCourseItemId");
$targetId = LibEnv::getEnvHttpGET("targetId");

// An ajax request parameter value is UTF-8 encoded
$elearningCourseItemId = utf8_decode($elearningCourseItemId);
$targetId = utf8_decode($targetId);

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
