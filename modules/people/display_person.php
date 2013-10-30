<?php

require_once("website.php");

$peopleId = LibEnv::getEnvHttpGET("peopleId");

// Prevent sql injection attacks as the id is always numeric
$peopleId = (int) $peopleId;

if (!$peopleId) {
  $str = LibHtml::urlRedirect("$gPeopleUrl/list.php", $gRedirectDelay);
  printMessage($str);
  exit;
  }

$gTemplate->setPageContent($peopleUtils->render($peopleId));
require_once($gTemplatePath . "render.php");

?>
