<?php

require_once("website.php");

$photoId = LibEnv::getEnvHttpGET("photoId");

// Prevent sql injection attacks as the id is always numeric
$photoId = (int) $photoId;

$str = $photoUtils->render($photoId);

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
