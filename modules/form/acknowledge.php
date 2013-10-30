<?php

require_once("website.php");

$websiteText = $languageUtils->getWebsiteText(__FILE__);

$formId = LibEnv::getEnvHttpGET("formId");

$acknowledge = '';
if ($form = $formUtils->selectById($formId)) {
  $currentLanguageCode = $languageUtils->getCurrentLanguageCode();
  $acknowledge = $languageUtils->getTextForLanguage($form->getAcknowledge(), $currentLanguageCode);
  }

if (!$acknowledge) {
  $acknowledge = $websiteText[0];
  }

$acknowledge = nl2br($acknowledge);

$str = "<div class='system'>"
  . "<div class='system_comment'>"
  . $acknowledge
  . "</div>"
  . "</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
