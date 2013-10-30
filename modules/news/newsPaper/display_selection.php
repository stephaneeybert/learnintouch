<?php

require_once("website.php");


$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
$newsFeedId = LibEnv::getEnvHttpGET("newsFeedId");
$newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");
$period = LibEnv::getEnvHttpGET("period");
$localEventStartDate = LibEnv::getEnvHttpGET("eventStartDate");
$localEventEndDate = LibEnv::getEnvHttpGET("eventEndDate");

// Prevent sql injection attacks as the id is always numeric
$newsPaperId = (int) $newsPaperId;
$newsFeedId = (int) $newsFeedId;
$newsHeadingId = (int) $newsHeadingId;

if ($newsPaperUtils->isSecured($newsPaperId)) {
  $userUtils->checkValidUserLogin();
}

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

$content = '';
if ($newsPaper = $newsPaperUtils->selectById($newsPaperId)) {
  $content = $newsPaperUtils->renderContent($newsPaper, $newsStories, true);

  if (!$content) {
    $content = $newsPaperUtils->websiteText[24];
  }
}

$gTemplate->setPageContent($content);

$newsTemplateModelId = $newsStoryUtils->getTemplateModel();
if ($newsTemplateModelId > 0) {
  $templateModelId = $newsTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
