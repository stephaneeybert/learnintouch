<?php

require_once("website.php");

$newsStoryId = LibEnv::getEnvHttpGET("newsStoryId");
$newsStoryImageId = LibEnv::getEnvHttpGET("newsStoryImageId");


if ($newsStoryUtils->isSecured($newsStoryId)) {
  $userUtils->checkValidUserLogin();
}

$gTemplate->setPageContent($newsStoryUtils->renderNewsStory($newsStoryId, $newsStoryImageId));

$newsTemplateModelId = $newsStoryUtils->getTemplateModel();
if ($newsTemplateModelId > 0) {
  $templateModelId = $newsTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
