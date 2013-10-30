<?php

require_once("website.php");

if (!isset($formId)) {
  $formId = LibEnv::getEnvHttpGET("formId");
}

// Prevent sql injection attacks as the id is always numeric
$formId = (int) $formId;

// Call the controller
require_once($gFormPath . "controller.php");

if ($form = $formUtils->selectById($formId)) {
  $gTemplate->setPageContent($formUtils->render($formId, $warnings));

  $preferenceUtils->init($dynpageUtils->preferences);
  if ($preferenceUtils->getValue("DYNPAGE_NAME_AS_TITLE")) {
    $name = $form->getName();
    if ($name) {
      $gTemplate->setPageTitle($name);
    }
  }
}

require_once($gTemplatePath . "render.php");

?>
