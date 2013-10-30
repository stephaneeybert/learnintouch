<?php

require_once("website.php");

$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
if (!$newsPaperId) {
  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
}
$newsHeadingId = LibEnv::getEnvHttpGET("newsHeadingId");
if (!$newsHeadingId) {
  $newsHeadingId = LibEnv::getEnvHttpPOST("newsHeadingId");
}

// Prevent sql injection attacks as the id is always numeric
$newsHeadingId = (int) $newsHeadingId;
$newsPaperId = (int) $newsPaperId;


if ($newsPaperUtils->isSecured($newsPaperId)) {
  $userUtils->checkValidUserLogin();
}

$gTemplate->setPageContent($newsPaperUtils->renderNewsHeadingStories($newsPaperId, $newsHeadingId));

$newsTemplateModelId = $newsStoryUtils->getTemplateModel();
if ($newsTemplateModelId > 0) {
  $templateModelId = $newsTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
