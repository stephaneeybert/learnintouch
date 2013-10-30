<?php

require_once("website.php");

$newsPaperId = LibEnv::getEnvHttpGET("newsPaperId");
if (!$newsPaperId) {
  $newsPaperId = LibEnv::getEnvHttpPOST("newsPaperId");
}

// Prevent sql injection attacks as the id is always numeric
$newsPaperId = (int) $newsPaperId;


if ($newsPaperUtils->isSecured($newsPaperId)) {
  $userUtils->checkValidUserLogin();
}

$gTemplate->setPageContent($newsPaperUtils->renderNewsPaperArchives($newsPaperId));

$newsTemplateModelId = $newsStoryUtils->getTemplateModel();
if ($newsTemplateModelId > 0) {
  $templateModelId = $newsTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
