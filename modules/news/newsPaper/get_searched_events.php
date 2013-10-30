<?PHP

require_once("website.php");

LibHtml::preventCaching();


$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
$newsFeedId = LibEnv::getEnvHttpGET("newsFeedId");
$newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");
$period = LibEnv::getEnvHttpGET("period");
$localEventStartDate = LibEnv::getEnvHttpGET("eventStartDate");
$localEventEndDate = LibEnv::getEnvHttpGET("eventEndDate");

// An ajax request parameter value is UTF-8 encoded
$newsPaperId = utf8_decode($newsPaperId);
$newsFeedId = utf8_decode($newsFeedId);
$newsHeadingId = utf8_decode($newsHeadingId);
$period = utf8_decode($period);
$localEventStartDate = utf8_decode($localEventStartDate);
$localEventEndDate = utf8_decode($localEventEndDate);

if ($localEventStartDate) {
  $eventStartDate = $clockUtils->localToSystemDate($localEventStartDate);
} else {
  $eventStartDate = '';
}
if ($localEventEndDate) {
  $eventEndDate = $clockUtils->localToSystemDate($localEventEndDate);
} else {
  $eventEndDate = '';
}

$newsStories = $newsPaperUtils->collectNewsStoriesForEventsOnSelection($newsPaperId, $newsHeadingId, $period, $eventStartDate, $eventEndDate);
$periodLabel = $newsPaperUtils->getPeriodLabel($period, $localEventStartDate, $localEventEndDate);

if ($newsFeedId && count($newsStories) > 0) {
  if ($newsFeed = $newsFeedUtils->selectById($newsFeedId)) {
    $content = $newsFeedUtils->renderFeedStories($newsFeed, $newsStories);
  }
} else {
  $content = '';
}

if (!$content) {
  $message = $newsPaperUtils->websiteText[24];
} else {
  $message = '';
}

$content = LibString::jsonEscapeLinebreak($content);
$content = LibString::escapeDoubleQuotes($content);

$responseText = <<<HEREDOC
{
"newsPaperId" : "$newsPaperId",
"newsFeedId" : "$newsFeedId",
"eventStartDate" : "$localEventStartDate",
"eventEndDate" : "$localEventEndDate",
"period" : "$period",
"periodLabel" : "$periodLabel",
"message" : "$message",
"content" : "$content"
}
HEREDOC;

print($responseText);

?>
