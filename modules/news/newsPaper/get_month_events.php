<?PHP

require_once("website.php");

LibHtml::preventCaching();


$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
$newsFeedId = LibEnv::getEnvHttpGET("newsFeedId");
$eventStartDate = LibEnv::getEnvHttpGET("eventStartDate");

if ($eventStartDate) {
  $newsStories = $newsPaperUtils->collectNewsStoriesForEventsForAMonth($newsPaperId, $eventStartDate);
}

$eventDates = '';
if ($newsFeedId && count($newsStories) > 0) {
  foreach ($newsStories as $newsStory) {
    $eventStartDate = $newsStory->getEventStartDate();
    $eventEndDate = $newsStory->getEventEndDate();
    $eventDate = substr($eventStartDate, 0, 10);
    while ($clockUtils->systemDateIsGreaterOrEqual($eventEndDate, $eventDate)) {
      if (!strstr($eventDates, $eventDate)) {
        $eventDates .= "\"$eventDate\",";
      }
      $eventDate = $clockUtils->incrementDays($eventDate, 1);
    }
  }
  $eventDates = substr($eventDates, 0, strlen($eventDates) - 1);
}

$responseText = <<<HEREDOC
{
"newsPaperId" : "$newsPaperId",
"newsFeedId" : "$newsFeedId",
"eventDates" : [ $eventDates ]
}
HEREDOC;

print($responseText);

?>
