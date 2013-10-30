<?php

require_once("website.php");


$gTemplate->setPageContent($newsPublicationUtils->renderList());

$newsTemplateModelId = $newsStoryUtils->getTemplateModel();
if ($newsTemplateModelId > 0) {
  $templateModelId = $newsTemplateModelId;
}

require_once($gTemplatePath . "render.php");

?>
