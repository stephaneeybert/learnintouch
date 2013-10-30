<?php

require_once("website.php");

if (!isset($newsPublicationId)) {
  $newsPublicationId = LibEnv::getEnvHttpGET("newsPublicationId");
  }

// Prevent sql injection attacks as the id is always numeric
$newsPublicationId = (int) $newsPublicationId;


$gTemplate->setPageContent($newsPaperUtils->renderList($newsPublicationId));

$newsTemplateModelId = $newsStoryUtils->getTemplateModel();
if ($newsTemplateModelId > 0) {
  $templateModelId = $newsTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
