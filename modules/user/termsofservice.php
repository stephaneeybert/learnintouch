<?php

require_once("website.php");

$str = "<div class='system'>";

$terms = $profileUtils->renderWebSiteTermsOfService();

$str .= $terms;

$str .= "</div>";

$gTemplate->setPageContent($str);
require_once($gTemplatePath . "render.php");

?>
