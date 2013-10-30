<?php

require_once("website.php");

$templateModelId = $templateUtils->getPhoneEntry();
$templateUtils->setCurrentModel($templateModelId);

$templateUtils->setPhoneClient();

$preferenceUtils = new PreferenceUtils($dynpageUtils->preferences);
$displayConstructionMessage = $preferenceUtils->getValue("DYNPAGE_WEBSITE_IN_CONSTRUCTION");

if ($displayConstructionMessage) {
  $str = nl2br($preferenceUtils->getValue("DYNPAGE_WEBSITE_IN_CONSTRUCTION_MESSAGE"));

  $gTemplate->setPageContent($str);
  require_once($gTemplatePath . "render.php");
} else {
  require_once($gTemplatePath . "display.php");
}

?>
